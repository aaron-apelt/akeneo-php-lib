<?php

declare(strict_types=1);

namespace AkeneoLib\Adapter;

use Akeneo\Pim\ApiClient\Api\AttributeOptionApiInterface;
use AkeneoLib\Adapter\Support\FluentAdapterResult;
use AkeneoLib\Entity\AttributeOption;
use AkeneoLib\Search\QueryParameter;
use AkeneoLib\Serializer\SerializerInterface;
use Generator;

class AttributeOptionAdapter implements AttributeOptionAdapterInterface
{
    use BatchableAdapterTrait;

    private string $attributeCode = '';

    public function __construct(
        private readonly AttributeOptionApiInterface $attributeOptionApi,
        private readonly SerializerInterface $serializer
    ) {}

    /**
     * {@inheritDoc}
     */
    public function getAttributeCode(): string
    {
        return $this->attributeCode;
    }

    /**
     * {@inheritDoc}
     */
    public function setAttributeCode(string $attributeCode): self
    {
        $this->attributeCode = $attributeCode;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function all(?QueryParameter $queryParameters = null): FluentAdapterResult
    {
        $queryParameters ??= new QueryParameter;
        $generator = function () use ($queryParameters): Generator {
            foreach ($this->attributeOptionApi->all($this->attributeCode, $this->batchSize, $queryParameters->toArray()) as $option) {
                yield $this->serializer->denormalize($option, AttributeOption::class);
            }
        };

        return new FluentAdapterResult($generator());
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $code): AttributeOption
    {
        $option = $this->attributeOptionApi->get($this->attributeCode, $code);

        return $this->serializer->denormalize($option, AttributeOption::class);
    }

    /**
     * {@inheritDoc}
     */
    public function stage(AttributeOption $option): void
    {
        $this->addPendingItem($option);
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
            $response = $this->attributeOptionApi->upsertList($this->attributeCode, $normalized);
            $this->triggerResponseCallback($response, $this->pendingItems);
            $this->clearPendingItems();
        }
    }
}
