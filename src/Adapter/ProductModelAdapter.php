<?php

declare(strict_types=1);

namespace AkeneoLib\Adapter;

use Akeneo\Pim\ApiClient\Api\ProductModelApiInterface;
use AkeneoLib\Entity\ProductModel;
use AkeneoLib\Search\QueryParameter;
use AkeneoLib\Serializer\SerializerInterface;
use DateTimeImmutable;
use Generator;
use Traversable;

class ProductModelAdapter implements ProductModelAdapterInterface
{
    private int $batchSize = 100;

    private array $productModels = [];

    /** @var callable|null */
    private $responseCallback = null;

    public function __construct(
        private readonly ProductModelApiInterface $productModelApi,
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
        foreach ($this->productModelApi->all($this->batchSize, $queryParameters->toArray()) as $productModel) {
            yield $this->serializer->denormalize($productModel, ProductModel::class);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $code): ProductModel
    {
        $productModel = $this->productModelApi->get($code);

        return $this->serializer->denormalize($productModel, ProductModel::class);
    }

    /**
     * {@inheritDoc}
     */
    public function stage(ProductModel $productModel): void
    {
        $this->productModels[] = $productModel;
        if (count($this->productModels) >= $this->batchSize) {
            $this->push();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function push(): void
    {
        if (! empty($this->productModels)) {
            $normalizedProductModels = $this->serializer->normalize($this->productModels);
            $response = $this->productModelApi->upsertList($normalizedProductModels);
            $this->triggerResponseCallback($response, $this->productModels);
            $this->productModels = [];
        }
    }

    private function triggerResponseCallback(Traversable $response, array $pushedProductModels): void
    {
        if ($this->responseCallback !== null) {
            call_user_func($this->responseCallback, $response, $pushedProductModels, new DateTimeImmutable);
        }
    }
}
