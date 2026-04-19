<?php

declare(strict_types=1);

namespace AkeneoLib\Adapter;

use Akeneo\Pim\ApiClient\Api\CategoryApiInterface;
use AkeneoLib\Adapter\Support\FluentAdapterResult;
use AkeneoLib\Entity\Category;
use AkeneoLib\Search\QueryParameter;
use AkeneoLib\Serializer\SerializerInterface;
use Generator;

class CategoryAdapter implements CategoryAdapterInterface
{
    use BatchableAdapterTrait;

    public function __construct(
        private readonly CategoryApiInterface $categoryApi,
        private readonly SerializerInterface $serializer
    ) {}

    /**
     * {@inheritDoc}
     */
    public function all(?QueryParameter $queryParameters = null): FluentAdapterResult
    {
        $queryParameters ??= new QueryParameter;
        $generator = function () use ($queryParameters): Generator {
            foreach ($this->categoryApi->all($this->batchSize, $queryParameters->toArray()) as $category) {
                yield $this->serializer->denormalize($category, Category::class);
            }
        };

        return new FluentAdapterResult($generator());
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $code): Category
    {
        $category = $this->categoryApi->get($code);

        return $this->serializer->denormalize($category, Category::class);
    }

    /**
     * {@inheritDoc}
     */
    public function stage(Category $category): void
    {
        $this->addPendingItem($category);
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
            $response = $this->categoryApi->upsertList($normalized);
            $this->triggerResponseCallback($response, $this->pendingItems);
            $this->clearPendingItems();
        }
    }
}
