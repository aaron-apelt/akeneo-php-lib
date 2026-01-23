<?php

declare(strict_types=1);

namespace AkeneoLib\Adapter;

use Akeneo\Pim\ApiClient\Exception\HttpException;
use AkeneoLib\Adapter\Support\FluentAdapterResult;
use AkeneoLib\Entity\AttributeOption;
use AkeneoLib\Exception\SerializationException;
use AkeneoLib\Search\QueryParameter;

interface AttributeOptionAdapterInterface
{
    /**
     * Get the batch size for upserting attribute options.
     */
    public function getBatchSize(): int;

    /**
     * Set the batch size for upserting attribute options.
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
     * Gets the attribute code for the upsert target.
     */
    public function getAttributeCode(): string;

    /**
     * Sets the attribute code for the upsert target.
     */
    public function setAttributeCode(string $attributeCode): self;

    /**
     * Receives all attribute options for the given queryParameters and return them denormalized inside a FluentAdapterResult.
     *
     * @return FluentAdapterResult<AttributeOption>
     *
     * @throws SerializationException if the serialization fails
     */
    public function all(?QueryParameter $queryParameters = null): FluentAdapterResult;

    /**
     * Receives an attribute option by a given code and denormalize it to an AttributeOption object.
     *
     * @throws HttpException if the request failed
     * @throws SerializationException if the serialization fails
     */
    public function get(string $code): AttributeOption;

    /**
     * Adds the attribute option to a queue. The queue is only pushed to
     * Akeneo if the batch size is reached. If you use this function make sure to call push() afterward.
     *
     * @throws SerializationException if normalization failed
     */
    public function stage(AttributeOption $option): void;

    /**
     * Upsert the normalized attribute options from the queue to Akeneo. Call this function after you used stage().
     */
    public function push(): void;
}
