<?php

declare(strict_types=1);

namespace AkeneoLib\Adapter;

use Akeneo\Pim\ApiClient\Api\FamilyVariantApiInterface;
use AkeneoLib\Adapter\Support\FluentAdapterResult;
use AkeneoLib\Entity\FamilyVariant;
use AkeneoLib\Search\QueryParameter;
use AkeneoLib\Serializer\SerializerInterface;
use Generator;

class FamilyVariantAdapter implements FamilyVariantAdapterInterface
{
    use BatchableAdapterTrait;

    private string $familyCode = '';

    public function __construct(
        private readonly FamilyVariantApiInterface $familyVariantApi,
        private readonly SerializerInterface $serializer
    ) {}

    /**
     * {@inheritDoc}
     */
    public function all(?QueryParameter $queryParameters = null): FluentAdapterResult
    {
        $queryParameters ??= new QueryParameter;
        $generator = function () use ($queryParameters): Generator {
            foreach ($this->familyVariantApi->all($this->familyCode, $this->batchSize, $queryParameters->toArray()) as $variant) {
                yield $this->serializer->denormalize($variant, FamilyVariant::class);
            }
        };

        return new FluentAdapterResult($generator());
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $code): FamilyVariant
    {
        $variant = $this->familyVariantApi->get($this->familyCode, $code);

        return $this->serializer->denormalize($variant, FamilyVariant::class);
    }

    /**
     * {@inheritDoc}
     */
    public function stage(FamilyVariant $familyVariant): void
    {
        $this->addPendingItem($familyVariant);
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
            $response = $this->familyVariantApi->upsertList($this->familyCode, $normalized);
            $this->triggerResponseCallback($response, $this->pendingItems);
            $this->clearPendingItems();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getFamilyCode(): string
    {
        return $this->familyCode;
    }

    /**
     * {@inheritDoc}
     */
    public function setFamilyCode(string $familyCode): self
    {
        $this->familyCode = $familyCode;

        return $this;
    }
}
