<?php

declare(strict_types=1);

namespace AkeneoLib\Adapter;

use Akeneo\Pim\ApiClient\Exception\HttpException;
use AkeneoLib\Adapter\Support\FluentAdapterResult;
use AkeneoLib\Entity\Asset;
use AkeneoLib\Exception\SerializationException;
use AkeneoLib\Search\QueryParameter;

interface AssetAdapterInterface
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
     * function (array $responses, array $upsertedObjects, \DateTimeImmutable $dateTime) {}
     */
    public function onResponse(callable $callback): self;

    /**
     * Gets the asset family code for the upsert target.
     */
    public function getAssetFamilyCode(): string;

    /**
     * Sets the asset family code for the upsert target.
     */
    public function setAssetFamilyCode(string $code): self;

    /**
     * Receives all assets for the given queryParameters and return them denormalized inside a FluentAdapterResult.
     *
     * @return FluentAdapterResult<Asset>
     *
     * @throws SerializationException if the serialization fails
     */
    public function all(?QueryParameter $queryParameters = null): FluentAdapterResult;

    /**
     * Receives an asset by a given code and denormalize it to an Asset object.
     *
     * @throws HttpException if the request failed
     * @throws SerializationException if the serialization fails
     */
    public function get(string $code): Asset;

    /**
     * Adds the asset to a queue. The queue is only pushed to
     * Akeneo if the batch size is reached. If you use this function make sure to call push() afterward.
     *
     * @throws SerializationException if normalization of the asset failed
     */
    public function stage(Asset $asset): void;

    /**
     * Upsert the normalized assets from the queue to Akeneo. Call this function after you used stage().
     */
    public function push(): void;
}
