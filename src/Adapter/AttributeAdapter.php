<?php

declare(strict_types=1);

namespace AkeneoLib\Adapter;

use Akeneo\Pim\ApiClient\Api\AttributeApiInterface;
use AkeneoLib\Adapter\Support\FluentAdapterResult;
use AkeneoLib\Entity\Attribute;
use AkeneoLib\Search\QueryParameter;
use AkeneoLib\Serializer\SerializerInterface;
use Generator;

class AttributeAdapter implements AttributeAdapterInterface
{
    use BatchableAdapterTrait;

    public function __construct(
        private readonly AttributeApiInterface $attributeApi,
        private readonly SerializerInterface $serializer
    ) {}

    /**
     * {@inheritDoc}
     */
    public function all(?QueryParameter $queryParameters = null): FluentAdapterResult
    {
        $queryParameters ??= new QueryParameter;
        $generator = function () use ($queryParameters): Generator {
            foreach ($this->attributeApi->all($this->batchSize, $queryParameters->toArray()) as $attribute) {
                yield $this->serializer->denormalize($attribute, Attribute::class);
            }
        };

        return new FluentAdapterResult($generator());
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $code): Attribute
    {
        $attribute = $this->attributeApi->get($code);

        return $this->serializer->denormalize($attribute, Attribute::class);
    }

    /**
     * {@inheritDoc}
     */
    public function stage(Attribute $attribute): void
    {
        $this->addPendingItem($attribute);
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
            $response = $this->attributeApi->upsertList($normalized);
            $this->triggerResponseCallback($response, $this->pendingItems);
            $this->clearPendingItems();
        }
    }
}
