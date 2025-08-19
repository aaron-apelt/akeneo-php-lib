<?php

declare(strict_types=1);

namespace AkeneoLib\Adapter;

use Akeneo\Pim\ApiClient\Exception\HttpException;
use AkeneoLib\Entity\Category;
use AkeneoLib\Exception\SerializationException;
use AkeneoLib\Search\QueryParameter;
use Generator;

interface CategoryAdapterInterface
{
    /**
     * Receives all categories for the given queryParameters and return them denormalized inside a Generator.
     *
     * @return Generator<Category>
     *
     * @throws SerializationException if the serialization fails
     */
    public function all(?QueryParameter $queryParameters = null): Generator;

    /**
     * Receives a category by code and denormalize it to a Category object.
     *
     * @throws HttpException if the request failed
     * @throws SerializationException if the serialization fails
     */
    public function get(string $code): Category;

    /**
     * Adds the category to a queue. The queue is only pushed to Akeneo if the
     * batch size is reached. If you use this function make sure to call push() afterward.
     *
     * @throws SerializationException if normalization of the Category failed
     */
    public function stage(Category $category): void;

    /**
     * Upsert the normalized categories from the queue to Akeneo. Call this function after you used stage().
     */
    public function push(): void;

    /**
     * Get the batch size for upserting categories.
     */
    public function getBatchSize(): int;

    /**
     * Set the batch size for upserting categories.
     */
    public function setBatchSize(int $batchSize): self;

    /**
     * Register a response handler.
     *
     * The handler function should look like this:
     * function (\Traversable $responses, array $upsertedObjects, \DateTimeImmutable $dateTime) {}
     */
    public function onResponse(callable $callback): self;
}
