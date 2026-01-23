<?php

declare(strict_types=1);

namespace AkeneoLib\Adapter;

use Akeneo\Pim\ApiClient\Exception\HttpException;
use AkeneoLib\Adapter\Support\FluentAdapterResult;
use AkeneoLib\Entity\ReferenceEntityRecord;
use AkeneoLib\Exception\SerializationException;
use AkeneoLib\Search\QueryParameter;

interface ReferenceEntityRecordAdapterInterface
{
    /**
     * Get the batch size for upserting reference entity records.
     */
    public function getBatchSize(): int;

    /**
     * Set the batch size for upserting reference entity records.
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
     * Gets the reference entity code for the upsert target.
     */
    public function getReferenceEntityCode(): string;

    /**
     * Sets the reference entity code for the upsert target.
     */
    public function setReferenceEntityCode(string $code): self;

    /**
     * Receives all reference entity records for the given queryParameters and return them denormalized inside a FluentAdapterResult.
     *
     * @return FluentAdapterResult<ReferenceEntityRecord>
     *
     * @throws SerializationException if the serialization fails
     */
    public function all(?QueryParameter $queryParameters = null): FluentAdapterResult;

    /**
     * Receives a reference entity record by a given code and denormalize it to a ReferenceEntityRecord object.
     *
     * @throws HttpException if the request failed
     * @throws SerializationException if the serialization fails
     */
    public function get(string $code): ReferenceEntityRecord;

    /**
     * Adds the reference entity record to a queue. The queue is only pushed to
     * Akeneo if the batch size is reached. If you use this function make sure to call push() afterward.
     *
     * @throws SerializationException if normalization of the reference entity failed
     */
    public function stage(ReferenceEntityRecord $referenceEntityRecord): void;

    /**
     * Upsert the normalized reference entity records from the queue to Akeneo. Call this function after you used stage().
     */
    public function push(): void;
}
