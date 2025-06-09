<?php

declare(strict_types=1);

namespace AkeneoLib\Adapter;

use Akeneo\Pim\ApiClient\Api\ProductApiInterface;
use AkeneoLib\Entity\Product;
use AkeneoLib\Search\QueryParameter;
use AkeneoLib\Serializer\SerializerInterface;
use DateTimeImmutable;
use Generator;
use Traversable;

class ProductAdapter implements ProductAdapterInterface
{
    private int $batchSize = 100;

    private array $products = [];

    /** @var callable|null */
    private $responseCallback = null;

    public function __construct(
        private readonly ProductApiInterface $productApi,
        private readonly SerializerInterface $serializer
    ) {}

    /**
     * {@inheritDoc}
     */
    public function getBatchSize(): int
    {
        return $this->batchSize;
    }

    /**
     * {@inheritDoc}
     */
    public function setBatchSize(int $batchSize): self
    {
        $this->batchSize = $batchSize;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function onResponse(callable $callback): self
    {
        $this->responseCallback = $callback;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function all(?QueryParameter $queryParameters = null): Generator
    {
        $queryParameters ??= new QueryParameter;
        foreach ($this->productApi->all($this->batchSize, $queryParameters->toArray()) as $product) {
            yield $this->serializer->denormalize($product, Product::class);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $identifier): Product
    {
        $product = $this->productApi->get($identifier);

        return $this->serializer->denormalize($product, Product::class);
    }

    /**
     * {@inheritDoc}
     */
    public function stage(Product $product): void
    {
        $this->products[] = $product;
        if (count($this->products) >= $this->batchSize) {
            $this->push();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function push(): void
    {
        if (! empty($this->products)) {
            $normalizedProducts = $this->serializer->normalize($this->products);
            $response = $this->productApi->upsertList($normalizedProducts);
            $this->triggerResponseCallback($response, $this->products);
            $this->products = [];
        }
    }

    private function triggerResponseCallback(Traversable $response, array $pushedProducts): void
    {
        if ($this->responseCallback !== null) {
            call_user_func($this->responseCallback, $response, $pushedProducts, new DateTimeImmutable);
        }
    }
}
