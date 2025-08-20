<?php

declare(strict_types=1);

namespace AkeneoLib\Adapter;

use Akeneo\Pim\ApiClient\Exception\HttpException;
use AkeneoLib\Entity\FamilyVariant;
use AkeneoLib\Exception\SerializationException;
use AkeneoLib\Search\QueryParameter;
use Generator;

interface FamilyVariantAdapterInterface
{
    /**
     * Get the batch size for upserting family variants.
     */
    public function getBatchSize(): int;

    /**
     * Set the batch size for upserting family variants.
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
     * Receives all family variants for the given queryParameters and return them denormalized inside a Generator.
     *
     * @return Generator<FamilyVariant>
     *
     * @throws SerializationException if the serialization fails
     */
    public function all(string $familyCode, ?QueryParameter $queryParameters = null): Generator;

    /**
     * Receives a family variant by a given code and denormalize it to a FamilyVariant object.
     *
     * @throws HttpException if the request failed
     * @throws SerializationException if the serialization fails
     */
    public function get(string $familyCode, string $code): FamilyVariant;

    /**
     * Adds the family variant to a queue. The queue is only pushed to Akeneo if the
     * batch size is reached. If you use this function make sure to call push() afterward.
     *
     * @throws SerializationException if normalization failed
     */
    public function stage(FamilyVariant $familyVariant): void;

    /**
     * Upsert the normalized family variants from the queue to Akeneo. Call this function after you used stage().
     */
    public function push(string $familyCode): void;
}
