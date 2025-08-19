<?php

declare(strict_types=1);

namespace AkeneoLib\Adapter;

use Akeneo\Pim\ApiClient\Exception\HttpException;
use AkeneoLib\Entity\ProductModel;
use AkeneoLib\Exception\SerializationException;
use AkeneoLib\Search\QueryParameter;
use Generator;

interface ProductModelAdapterInterface
{
    /**
     * Get the batch size for upserting product models.
     */
    public function getBatchSize(): int;

    /**
     * Set the batch size for upserting product models.
     */
    public function setBatchSize(int $batchSize): self;

    /**
     * Register a response handler.
     *
     * The handler function should look like this:
     * function (\Traversable $responses, array $upsertedObjects, \DateTimeImmutable $dateTime) {}
     */
    public function onResponse(callable $callback): self;

    /**
     * Receives all product models for the given queryParameters and return them denormalized inside a Generator.
     *
     * @return Generator<ProductModel>
     *
     * @throws SerializationException if the serialization fails
     */
    public function all(?QueryParameter $queryParameters = null): Generator;

    /**
     * Receives a product model by a given code and denormalize it to a ProductModel object.
     *
     * @throws HttpException if the request failed
     * @throws SerializationException if the serialization fails
     */
    public function get(string $code): ProductModel;

    /**
     * Adds the product model to a queue. The queue is only pushed to Akeneo if the
     * batch size is reached. If you use this function make sure to call push() afterward.
     *
     * @throws SerializationException if normalization of the Product failed
     */
    public function stage(ProductModel $productModel): void;

    /**
     * Upsert the normalized product models from the queue to Akeneo. Call this function after you used stage().
     */
    public function push(): void;
}
