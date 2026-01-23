<?php

declare(strict_types=1);

namespace AkeneoLib\Adapter;

use Akeneo\Pim\ApiClient\Exception\HttpException;
use AkeneoLib\Adapter\Support\FluentAdapterResult;
use AkeneoLib\Entity\Family;
use AkeneoLib\Exception\SerializationException;
use AkeneoLib\Search\QueryParameter;

interface FamilyAdapterInterface
{
    /**
     * Get the batch size for upserting families.
     */
    public function getBatchSize(): int;

    /**
     * Set the batch size for upserting families.
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
     * Receives all families for the given queryParameters and return them denormalized inside a FluentAdapterResult.
     *
     * @return FluentAdapterResult<Family>
     *
     * @throws SerializationException if the serialization fails
     */
    public function all(?QueryParameter $queryParameters = null): FluentAdapterResult;

    /**
     * Receives a family by a given code and denormalize it to a Family object.
     *
     * @throws HttpException if the request failed
     * @throws SerializationException if the serialization fails
     */
    public function get(string $code): Family;

    /**
     * Adds the family to a queue. The queue is only pushed to Akeneo if the
     * batch size is reached. If you use this function make sure to call push() afterward.
     *
     * @throws SerializationException if normalization of the Family failed
     */
    public function stage(Family $family): void;

    /**
     * Upsert the normalized families from the queue to Akeneo. Call this function after you used stage().
     */
    public function push(): void;
}
