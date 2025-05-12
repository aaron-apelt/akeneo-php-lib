<?php

namespace AkeneoLib\Adapter;

use AkeneoLib\Entity\Product;
use Generator;

interface ProductAdapterInterface
{
    /**
     * Get the batch size for upserting products.
     */
    public function getBatchSize(): int;

    /**
     * Set the batch size for upserting products.
     */
    public function setBatchSize(int $batchSize): self;

    /**
     * Register a response handler.
     *
     * @param callable $callback
     */
    public function onResponse(callable $callback): self;

    /**
     * Receives all products for the given queryParameters and return them denormalized inside a Generator.
     *
     * @return Generator<Product>
     * @throws ExceptionInterface if the serialization fails
     *
     */
    public function all(array $queryParameters = []): Generator;

    /**
     * Receives a product by a given identifier and denormalize it to a Product object.
     *
     * @throws HttpException if the request failed
     * @throws ExceptionInterface if the serialization fails
     */
    public function get(string $identifier): Product;

    /**
     * This function normalizes the given product and adds it to a queue. The queue is only pushed to Akeneo if the
     * batch size is reached. If you use this function make sure to call push() afterward.
     *
     * @throws ExceptionInterface if normalization of the Product failed
     */
    public function stage(Product $product): void;

    /**
     * Upsert the normalized products from the queue to Akeneo. Call this function after you used stage().
     */
    public function push(): void;
}