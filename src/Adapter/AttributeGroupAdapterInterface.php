<?php

declare(strict_types=1);

namespace AkeneoLib\Adapter;

use Akeneo\Pim\ApiClient\Exception\HttpException;
use AkeneoLib\Adapter\Support\FluentAdapterResult;
use AkeneoLib\Entity\AttributeGroup;
use AkeneoLib\Exception\SerializationException;
use AkeneoLib\Search\QueryParameter;

interface AttributeGroupAdapterInterface
{
    /**
     * Get the batch size for upserting attribute groups.
     */
    public function getBatchSize(): int;

    /**
     * Set the batch size for upserting attribute groups.
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
     * Receives all attribute groups for the given queryParameters and return them denormalized inside a FluentAdapterResult.
     *
     * @return FluentAdapterResult<AttributeGroup>
     *
     * @throws SerializationException
     */
    public function all(?QueryParameter $queryParameters = null): FluentAdapterResult;

    /**
     * Receives an attribute group by a given code and denormalize it to an AttributeGroup object.
     *
     * @throws HttpException
     * @throws SerializationException
     */
    public function get(string $code): AttributeGroup;

    /**
     * Adds the attribute group to a queue. The queue is only pushed to
     * Akeneo if the batch size is reached. If you use this function make sure to call push() afterward.
     *
     * @throws SerializationException
     */
    public function stage(AttributeGroup $attributeGroup): void;

    /**
     * Upsert the normalized attribute groups from the queue to Akeneo. Call this function after you used stage().
     */
    public function push(): void;
}
