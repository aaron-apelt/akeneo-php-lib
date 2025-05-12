<?php

declare(strict_types=1);

namespace AkeneoLib\Adapter;

use Akeneo\Pim\ApiClient\Api\ProductApiInterface;
use AkeneoLib\Entity\Product;
use App\Serializer\Akeneo\Serializer;
use DateTimeImmutable;

class ProductAdapter implements ProductAdapterInterface
{
    protected int $batchSize = 100;
    protected array $products = [];
    /** @var callable|null */
    protected $responseCallback = null;

    public function __construct(
        protected readonly ProductApiInterface $productApi,
        protected readonly Serializer $serializer
    ) {
    }

    /**
     * @inheritDoc
     */
    public function getBatchSize(): int
    {
        return $this->batchSize;
    }

    /**
     * @inheritDoc
     */
    public function setBatchSize(int $batchSize): self
    {
        $this->batchSize = $batchSize;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function onResponse(callable $callback): self
    {
        $this->responseCallback = $callback;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function all(array $queryParameters = []): \Generator
    {
        foreach ($this->productApi->all(100, $queryParameters) as $product) {
            yield $this->serializer->denormalize($product, Product::class);
        }
    }

    /**
     * @inheritDoc
     */
    public function get(string $identifier): Product
    {
        $product = $this->productApi->get($identifier);
        return $this->serializer->denormalize($product, Product::class);
    }

    /**
     * @inheritDoc
     */
    public function stage(Product $product): void
    {
        $this->products[] = $this->serializer->normalize($product);
        if (count($this->products) >= $this->batchSize) {
            $this->push();
        }
    }

    /**
     * @inheritDoc
     */
    public function push(): void
    {
        if (!empty($this->products)) {
            $response = $this->productApi->upsertList($this->products);
            $this->triggerResponseCallback(iterator_to_array($response));
            $this->products = [];
        }
    }

    private function triggerResponseCallback(array $response): void
    {
        if ($this->responseCallback !== null) {
            call_user_func($this->responseCallback, $response, $this->products, new DateTimeImmutable());
        }
    }
}