<?php

declare(strict_types=1);

namespace AkeneoLib\Adapter;

use Akeneo\Pim\ApiClient\Api\CategoryApiInterface;
use AkeneoLib\Adapter\Support\FluentAdapterResult;
use AkeneoLib\Entity\Category;
use AkeneoLib\Search\QueryParameter;
use AkeneoLib\Serializer\SerializerInterface;
use DateTimeImmutable;
use Generator;
use Traversable;

class CategoryAdapter implements CategoryAdapterInterface
{
    private int $batchSize = 100;

    private array $categories = [];

    /** @var callable|null */
    private $responseCallback = null;

    public function __construct(
        private readonly CategoryApiInterface $categoryApi,
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
        $this->categories[] = $category;
        if (count($this->categories) >= $this->batchSize) {
            $this->push();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function push(): void
    {
        if (! empty($this->categories)) {
            $normalizedCategories = $this->serializer->normalize($this->categories);
            $response = $this->categoryApi->upsertList($normalizedCategories);
            $this->triggerResponseCallback($response, $this->categories);
            $this->categories = [];
        }
    }

    private function triggerResponseCallback(Traversable $response, array $pushedCategories): void
    {
        if ($this->responseCallback !== null) {
            call_user_func($this->responseCallback, $response, $pushedCategories, new DateTimeImmutable);
        }
    }
}
