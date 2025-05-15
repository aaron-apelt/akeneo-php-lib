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
    protected int $batchSize = 100;

    protected array $products = [];

    /** @var callable|null */
    protected $responseCallback = null;

    public function __construct(
        protected readonly ProductApiInterface $productApi,
        protected readonly SerializerInterface $serializer
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
    public function all(QueryParameter $queryParameters = new QueryParameter): Generator
    {
        foreach ($this->productApi->all(100, $queryParameters->toArray()) as $product) {
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
            $this->triggerResponseCallback($response);
            $this->products = [];
        }
    }

    private function triggerResponseCallback(Traversable $response): void
    {
        if ($this->responseCallback !== null) {
            call_user_func($this->responseCallback, $response, $this->products, new DateTimeImmutable);
        }
    }
}
