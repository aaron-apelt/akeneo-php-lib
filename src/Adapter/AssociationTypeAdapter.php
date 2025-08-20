<?php

declare(strict_types=1);

namespace AkeneoLib\Adapter;

use Akeneo\Pim\ApiClient\Api\AssociationTypeApiInterface;
use AkeneoLib\Entity\AssociationType;
use AkeneoLib\Search\QueryParameter;
use AkeneoLib\Serializer\SerializerInterface;
use DateTimeImmutable;
use Generator;
use Traversable;

class AssociationTypeAdapter implements AssociationTypeAdapterInterface
{
    private int $batchSize = 100;

    private array $associationTypes = [];

    /** @var callable|null */
    private $responseCallback = null;

    public function __construct(
        private readonly AssociationTypeApiInterface $associationTypeApi,
        private readonly SerializerInterface $serializer
    ) {}

    public function getBatchSize(): int
    {
        return $this->batchSize;
    }

    /**
     * {@inheritDoc}
     */
    public function setBatchSize(int $batchSize): self
    {
        $this->batchSize = $batchSize;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function onResponse(callable $callback): self
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
        foreach ($this->associationTypeApi->all($this->batchSize, $queryParameters->toArray()) as $associationType) {
            yield $this->serializer->denormalize($associationType, AssociationType::class);
        }
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
        $this->associationTypes[] = $associationType;
        if (count($this->associationTypes) >= $this->batchSize) {
            $this->push();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function push(): void
    {
        if (! empty($this->associationTypes)) {
            $normalized = $this->serializer->normalize($this->associationTypes);
            $response = $this->associationTypeApi->upsertList($normalized);
            $this->triggerResponseCallback($response, $this->associationTypes);
            $this->associationTypes = [];
        }
    }

    private function triggerResponseCallback(Traversable $response, array $pushedItems): void
    {
        if ($this->responseCallback !== null) {
            call_user_func($this->responseCallback, $response, $pushedItems, new DateTimeImmutable);
        }
    }
}
