<?php

declare(strict_types=1);

namespace AkeneoLib\Adapter;

use Akeneo\Pim\ApiClient\Exception\HttpException;
use AkeneoLib\Entity\Attribute;
use AkeneoLib\Exception\SerializationException;
use AkeneoLib\Search\QueryParameter;
use Generator;

interface AttributeAdapterInterface
{
    /**
     * Get the batch size for upserting attributes.
     */
    public function getBatchSize(): int;

    /**
     * Set the batch size for upserting attributes.
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
     * Receives all attributes and return them denormalized inside a Generator.
     *
     * @return Generator<Attribute>
     *
     * @throws SerializationException if the serialization fails
     */
    public function all(?QueryParameter $queryParameters = null): Generator;

    /**
     * Receives an attribute by a given identifier and denormalize it to an Attribute object.
     *
     * @throws HttpException if the request failed
     * @throws SerializationException if the serialization fails
     */
    public function get(string $code): Attribute;

    /**
     * Adds the attribute to a queue. The queue is only pushed to Akeneo if the
     * batch size is reached. If you use this function make sure to call push() afterward.
     *
     * @throws SerializationException if normalization of the Attribute failed
     */
    public function stage(Attribute $attribute): void;

    /**
     * Upsert the normalized attributes from the queue to Akeneo. Call this function after you used stage().
     */
    public function push(): void;
}
