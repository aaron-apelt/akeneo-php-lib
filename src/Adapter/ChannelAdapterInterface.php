<?php

declare(strict_types=1);

namespace AkeneoLib\Adapter;

use Akeneo\Pim\ApiClient\Exception\HttpException;
use AkeneoLib\Adapter\Support\FluentAdapterResult;
use AkeneoLib\Entity\Channel;
use AkeneoLib\Exception\SerializationException;
use AkeneoLib\Search\QueryParameter;

interface ChannelAdapterInterface
{
    /**
     * Get the batch size for upserting channels.
     */
    public function getBatchSize(): int;

    /**
     * Set the batch size for upserting channels.
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
     * Receives all channels for the given queryParameters and return them denormalized inside a FluentAdapterResult.
     *
     * @return FluentAdapterResult<Channel>
     *
     * @throws SerializationException
     */
    public function all(?QueryParameter $queryParameters = null): FluentAdapterResult;

    /**
     * Receives a channel by code and denormalize it to a Channel object.
     *
     * @throws HttpException
     * @throws SerializationException
     */
    public function get(string $code): Channel;

    /**
     * Adds the channel to a queue. The queue is only pushed to Akeneo if the
     * batch size is reached. If you use this function make sure to call push() afterward.
     *
     * @throws SerializationException
     */
    public function stage(Channel $channel): void;

    /**
     * Upsert the normalized channels from the queue to Akeneo. Call this function after you used stage().
     */
    public function push(): void;
}
