<?php

declare(strict_types=1);

namespace AkeneoLib\Adapter;

use Akeneo\Pim\ApiClient\Api\FamilyApiInterface;
use AkeneoLib\Adapter\Support\FluentAdapterResult;
use AkeneoLib\Entity\Family;
use AkeneoLib\Search\QueryParameter;
use AkeneoLib\Serializer\SerializerInterface;
use Generator;

class FamilyAdapter implements FamilyAdapterInterface
{
    use BatchableAdapterTrait;

    public function __construct(
        private readonly FamilyApiInterface $familyApi,
        private readonly SerializerInterface $serializer
    ) {}

    /**
     * {@inheritDoc}
     */
    public function all(?QueryParameter $queryParameters = null): FluentAdapterResult
    {
        $queryParameters ??= new QueryParameter;
        $generator = function () use ($queryParameters): Generator {
            foreach ($this->familyApi->all($this->batchSize, $queryParameters->toArray()) as $family) {
                yield $this->serializer->denormalize($family, Family::class);
            }
        };

        return new FluentAdapterResult($generator());
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $code): Family
    {
        $family = $this->familyApi->get($code);

        return $this->serializer->denormalize($family, Family::class);
    }

    /**
     * {@inheritDoc}
     */
    public function stage(Family $family): void
    {
        $this->addPendingItem($family);
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
            $response = $this->familyApi->upsertList($normalized);
            $this->triggerResponseCallback($response, $this->pendingItems);
            $this->clearPendingItems();
        }
    }
}
