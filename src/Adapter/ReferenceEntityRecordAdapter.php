<?php

declare(strict_types=1);

namespace AkeneoLib\Adapter;

use Akeneo\Pim\ApiClient\Api\ReferenceEntityRecordApiInterface;
use AkeneoLib\Entity\ReferenceEntityRecord;
use AkeneoLib\Search\QueryParameter;
use AkeneoLib\Serializer\SerializerInterface;
use DateTimeImmutable;
use Generator;

class ReferenceEntityRecordAdapter implements ReferenceEntityRecordAdapterInterface
{
    private int $batchSize = 100;

    private string $referenceEntityCode = '';

    private array $referenceEntityRecords = [];

    /** @var callable|null */
    private $responseCallback = null;

    public function __construct(
        private readonly ReferenceEntityRecordApiInterface $referenceEntityRecordApi,
        private readonly SerializerInterface $serializer
    ) {}

    /**
     * {@inheritDoc}
     */
    public function onResponse(callable $callback): ReferenceEntityRecordAdapterInterface
    {
        $this->responseCallback = $callback;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function all(?QueryParameter $queryParameters = null): Generator
    {
        $queryParameters ??= new QueryParameter;
        foreach ($this->referenceEntityRecordApi->all($this->referenceEntityCode, $queryParameters->toArray()) as $product) {
            yield $this->serializer->denormalize($product, ReferenceEntityRecord::class);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $code): ReferenceEntityRecord
    {
        $product = $this->referenceEntityRecordApi->get($this->referenceEntityCode, $code);

        return $this->serializer->denormalize($product, ReferenceEntityRecord::class);
    }

    /**
     * {@inheritDoc}
     */
    public function stage(ReferenceEntityRecord $referenceEntityRecord): void
    {
        $this->referenceEntityRecords[] = $referenceEntityRecord;
        if (count($this->referenceEntityRecords) >= $this->batchSize) {
            $this->push();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function push(): void
    {
        if (! empty($this->referenceEntityRecords)) {
            $normalizedReferenceEntityRecords = $this->serializer->normalize($this->referenceEntityRecords);
            $response = $this->referenceEntityRecordApi->upsertList($this->referenceEntityCode, $normalizedReferenceEntityRecords);
            $this->triggerResponseCallback($response, $this->referenceEntityRecords);
            $this->referenceEntityRecords = [];
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
    public function setReferenceEntityCode(string $code): ReferenceEntityRecordAdapterInterface
    {
        $this->referenceEntityCode = $code;

        return $this;
    }

    private function triggerResponseCallback(array $response, array $pushedProducts): void
    {
        if ($this->responseCallback !== null) {
            call_user_func($this->responseCallback, $response, $pushedProducts, new DateTimeImmutable);
        }
    }
}
