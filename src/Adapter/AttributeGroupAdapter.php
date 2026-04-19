<?php

declare(strict_types=1);

namespace AkeneoLib\Adapter;

use Akeneo\Pim\ApiClient\Api\AttributeGroupApiInterface;
use AkeneoLib\Adapter\Support\FluentAdapterResult;
use AkeneoLib\Entity\AttributeGroup;
use AkeneoLib\Search\QueryParameter;
use AkeneoLib\Serializer\SerializerInterface;
use Generator;

class AttributeGroupAdapter implements AttributeGroupAdapterInterface
{
    use BatchableAdapterTrait;

    public function __construct(
        private readonly AttributeGroupApiInterface $attributeGroupApi,
        private readonly SerializerInterface $serializer
    ) {}

    /**
     * {@inheritDoc}
     */
    public function all(?QueryParameter $queryParameters = null): FluentAdapterResult
    {
        $queryParameters ??= new QueryParameter;
        $generator = function () use ($queryParameters): Generator {
            foreach ($this->attributeGroupApi->all($this->batchSize, $queryParameters->toArray()) as $group) {
                yield $this->serializer->denormalize($group, AttributeGroup::class);
            }
        };

        return new FluentAdapterResult($generator());
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $code): AttributeGroup
    {
        $group = $this->attributeGroupApi->get($code);

        return $this->serializer->denormalize($group, AttributeGroup::class);
    }

    /**
     * {@inheritDoc}
     */
    public function stage(AttributeGroup $attributeGroup): void
    {
        $this->addPendingItem($attributeGroup);
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
            $response = $this->attributeGroupApi->upsertList($normalized);
            $this->triggerResponseCallback($response, $this->pendingItems);
            $this->clearPendingItems();
        }
    }
}
