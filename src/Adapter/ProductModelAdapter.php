<?php

declare(strict_types=1);

namespace AkeneoLib\Adapter;

use Akeneo\Pim\ApiClient\Api\ProductModelApiInterface;
use AkeneoLib\Adapter\Support\FluentAdapterResult;
use AkeneoLib\Entity\ProductModel;
use AkeneoLib\Search\QueryParameter;
use AkeneoLib\Serializer\SerializerInterface;
use Generator;

class ProductModelAdapter implements ProductModelAdapterInterface
{
    use BatchableAdapterTrait;

    public function __construct(
        private readonly ProductModelApiInterface $productModelApi,
        private readonly SerializerInterface $serializer
    ) {}

    /**
     * {@inheritDoc}
     */
    public function all(?QueryParameter $queryParameters = null): FluentAdapterResult
    {
        $queryParameters ??= new QueryParameter;
        $generator = function () use ($queryParameters): Generator {
            foreach ($this->productModelApi->all($this->batchSize, $queryParameters->toArray()) as $productModel) {
                yield $this->serializer->denormalize($productModel, ProductModel::class);
            }
        };

        return new FluentAdapterResult($generator());
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
        $this->addPendingItem($productModel);
        if ($this->isPendingBatchFull()) {
            $this->push();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function push(): void
    {
        if ($this->hasPendingItems()) {
            $normalized = $this->serializer->normalize($this->pendingItems);
            $response = $this->productModelApi->upsertList($normalized);
            $this->triggerResponseCallback($response, $this->pendingItems);
            $this->clearPendingItems();
        }
    }
}
