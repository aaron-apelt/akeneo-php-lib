<?php

declare(strict_types=1);

namespace AkeneoLib\Adapter;

use Akeneo\Pim\ApiClient\Api\ReferenceEntityRecordApiInterface;
use AkeneoLib\Adapter\Support\FluentAdapterResult;
use AkeneoLib\Entity\ReferenceEntityRecord;
use AkeneoLib\Search\QueryParameter;
use AkeneoLib\Serializer\SerializerInterface;
use Generator;

class ReferenceEntityRecordAdapter implements ReferenceEntityRecordAdapterInterface
{
    use BatchableAdapterTrait;

    private string $referenceEntityCode = '';

    public function __construct(
        private readonly ReferenceEntityRecordApiInterface $referenceEntityRecordApi,
        private readonly SerializerInterface $serializer
    ) {}

    /**
     * {@inheritDoc}
     */
    public function all(?QueryParameter $queryParameters = null): FluentAdapterResult
    {
        $queryParameters ??= new QueryParameter;
        $generator = function () use ($queryParameters): Generator {
            foreach ($this->referenceEntityRecordApi->all($this->referenceEntityCode, $queryParameters->toArray()) as $referenceEntityRecord) {
                yield $this->serializer->denormalize($referenceEntityRecord, ReferenceEntityRecord::class, ['scopeName' => 'channel']);
            }
        };

        return new FluentAdapterResult($generator());
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $code): ReferenceEntityRecord
    {
        $referenceEntityRecord = $this->referenceEntityRecordApi->get($this->referenceEntityCode, $code);

        return $this->serializer->denormalize($referenceEntityRecord, ReferenceEntityRecord::class, ['scopeName' => 'channel']);
    }

    /**
     * {@inheritDoc}
     */
    public function stage(ReferenceEntityRecord $referenceEntityRecord): void
    {
        $this->addPendingItem($referenceEntityRecord);
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
            $normalized = $this->serializer->normalize($this->pendingItems, ['scopeName' => 'channel']);
            $response = $this->referenceEntityRecordApi->upsertList($this->referenceEntityCode, $normalized);
            $this->triggerResponseCallback($response, $this->pendingItems);
            $this->clearPendingItems();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getReferenceEntityCode(): string
    {
        return $this->referenceEntityCode;
    }

    /**
     * {@inheritDoc}
     */
    public function setReferenceEntityCode(string $code): self
    {
        $this->referenceEntityCode = $code;

        return $this;
    }
}
