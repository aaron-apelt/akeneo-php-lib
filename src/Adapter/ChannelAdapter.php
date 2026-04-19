<?php

declare(strict_types=1);

namespace AkeneoLib\Adapter;

use Akeneo\Pim\ApiClient\Api\ChannelApiInterface;
use AkeneoLib\Adapter\Support\FluentAdapterResult;
use AkeneoLib\Entity\Channel;
use AkeneoLib\Search\QueryParameter;
use AkeneoLib\Serializer\SerializerInterface;
use Generator;

class ChannelAdapter implements ChannelAdapterInterface
{
    use BatchableAdapterTrait;

    public function __construct(
        private readonly ChannelApiInterface $channelApi,
        private readonly SerializerInterface $serializer
    ) {}

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
        $this->addPendingItem($channel);
        if ($this->isPendingBatchFull()) {
            $this->push();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function push(): void
    {
        if ($this->hasPendingItems()) {
            $normalized = $this->serializer->normalize($this->pendingItems);
            $response = $this->channelApi->upsertList($normalized);
            $this->triggerResponseCallback($response, $this->pendingItems);
            $this->clearPendingItems();
        }
    }
}
