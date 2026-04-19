<?php

declare(strict_types=1);

namespace AkeneoLib\Adapter;

use Akeneo\Pim\ApiClient\Api\AssociationTypeApiInterface;
use AkeneoLib\Adapter\Support\FluentAdapterResult;
use AkeneoLib\Entity\AssociationType;
use AkeneoLib\Search\QueryParameter;
use AkeneoLib\Serializer\SerializerInterface;
use Generator;

class AssociationTypeAdapter implements AssociationTypeAdapterInterface
{
    use BatchableAdapterTrait;

    public function __construct(
        private readonly AssociationTypeApiInterface $associationTypeApi,
        private readonly SerializerInterface $serializer
    ) {}

    /**
     * {@inheritDoc}
     */
    public function all(?QueryParameter $queryParameters = null): FluentAdapterResult
    {
        $queryParameters ??= new QueryParameter;
        $generator = function () use ($queryParameters): Generator {
            foreach ($this->associationTypeApi->all($this->batchSize, $queryParameters->toArray()) as $associationType) {
                yield $this->serializer->denormalize($associationType, AssociationType::class);
            }
        };

        return new FluentAdapterResult($generator());
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $code): AssociationType
    {
        $associationType = $this->associationTypeApi->get($code);

        return $this->serializer->denormalize($associationType, AssociationType::class);
    }

    /**
     * {@inheritDoc}
     */
    public function stage(AssociationType $associationType): void
    {
        $this->addPendingItem($associationType);
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
            $response = $this->associationTypeApi->upsertList($normalized);
            $this->triggerResponseCallback($response, $this->pendingItems);
            $this->clearPendingItems();
        }
    }
}
