<?php

declare(strict_types=1);

namespace AkeneoLib\Adapter;

use Akeneo\Pim\ApiClient\Api\ChannelApiInterface;
use AkeneoLib\Adapter\Support\FluentAdapterResult;
use AkeneoLib\Entity\Channel;
use AkeneoLib\Search\QueryParameter;
use AkeneoLib\Serializer\SerializerInterface;
use DateTimeImmutable;
use Generator;
use Traversable;

class ChannelAdapter implements ChannelAdapterInterface
{
    private int $batchSize = 100;

    private array $channels = [];

    /** @var callable|null */
    private $responseCallback = null;

    public function __construct(
        private readonly ChannelApiInterface $channelApi,
        private readonly SerializerInterface $serializer
    ) {}

    /**
     * {@inheritDoc}
     */
    public function getBatchSize(): int
    {
        return $this->batchSize;
    }

    /**
     * {@inheritDoc}
     */
    public function setBatchSize(int $batchSize): self
    {
        $this->batchSize = $batchSize;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function onResponse(callable $callback): self
    {
        $this->responseCallback = $callback;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function all(?QueryParameter $queryParameters = null): FluentAdapterResult
    {
        $queryParameters ??= new QueryParameter;
        $generator = function () use ($queryParameters): Generator {
            foreach ($this->channelApi->all($this->batchSize, $queryParameters->toArray()) as $channel) {
                yield $this->serializer->denormalize($channel, Channel::class);
            }
        };

        return new FluentAdapterResult($generator());
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $code): Channel
    {
        $channel = $this->channelApi->get($code);

        return $this->serializer->denormalize($channel, Channel::class);
    }

    /**
     * {@inheritDoc}
     */
    public function stage(Channel $channel): void
    {
        $this->channels[] = $channel;
        if (count($this->channels) >= $this->batchSize) {
            $this->push();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function push(): void
    {
        if (! empty($this->channels)) {
            $normalized = $this->serializer->normalize($this->channels);
            $response = $this->channelApi->upsertList($normalized);
            $this->triggerResponseCallback($response, $this->channels);
            $this->channels = [];
        }
    }

    private function triggerResponseCallback(Traversable $response, array $pushedItems): void
    {
        if ($this->responseCallback !== null) {
            call_user_func($this->responseCallback, $response, $pushedItems, new DateTimeImmutable);
        }
    }
}
