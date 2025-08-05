<?php

declare(strict_types=1);

namespace AkeneoLib\Adapter;

use Akeneo\Pim\ApiClient\Api\ReferenceEntityApiInterface;
use AkeneoLib\Entity\ReferenceEntity;
use AkeneoLib\Search\QueryParameter;
use AkeneoLib\Serializer\SerializerInterface;
use Generator;

readonly class ReferenceEntityAdapter implements ReferenceEntityAdapterInterface
{
    public function __construct(
        private ReferenceEntityApiInterface $referenceEntityApi,
        private SerializerInterface $serializer
    ) {}

    /**
     * {@inheritDoc}
     */
    public function all(?QueryParameter $queryParameters = null): Generator
    {
        $queryParameters ??= new QueryParameter;
        foreach ($this->referenceEntityApi->all($queryParameters->toArray()) as $product) {
            yield $this->serializer->denormalize($product, ReferenceEntity::class);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $code): ReferenceEntity
    {
        $referenceEntity = $this->referenceEntityApi->get($code);

        return $this->serializer->denormalize($referenceEntity, ReferenceEntity::class);
    }

    /**
     * {@inheritDoc}
     */
    public function upsert(ReferenceEntity $referenceEntity): void
    {
        $normalizedReferenceEntity = $this->serializer->normalize($referenceEntity);

        $this->referenceEntityApi->upsert($referenceEntity->getCode(), $normalizedReferenceEntity);
    }
}
