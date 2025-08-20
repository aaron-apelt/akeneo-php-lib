<?php

declare(strict_types=1);

namespace AkeneoLib\Adapter;

use Akeneo\Pim\ApiClient\Exception\HttpException;
use AkeneoLib\Entity\AssociationType;
use AkeneoLib\Exception\SerializationException;
use AkeneoLib\Search\QueryParameter;
use Generator;

interface AssociationTypeAdapterInterface
{
    /**
     * Get the batch size for upserting association types.
     */
    public function getBatchSize(): int;

    /**
     * Set the batch size for upserting association types.
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
     * Receives all association types for the given queryParameters and return them denormalized inside a Generator.
     *
     * @return Generator<AssociationType>
     *
     * @throws SerializationException
     */
    public function all(?QueryParameter $queryParameters = null): Generator;

    /**
     * Receives an association type by a given code and denormalize it to an AssociationType object.
     *
     * @throws HttpException
     * @throws SerializationException
     */
    public function get(string $code): AssociationType;

    /**
     * Adds the association type to a queue. The queue is only pushed to
     * Akeneo if the batch size is reached. If you use this function make sure to call push() afterward.
     *
     * @throws SerializationException
     */
    public function stage(AssociationType $associationType): void;

    /**
     * Upsert the normalized association types from the queue to Akeneo. Call this function after you used stage().
     */
    public function push(): void;
}
