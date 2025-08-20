<?php

declare(strict_types=1);

namespace AkeneoLib\Adapter;

use Akeneo\Pim\ApiClient\Api\FamilyApiInterface;
use AkeneoLib\Entity\Family;
use AkeneoLib\Search\QueryParameter;
use AkeneoLib\Serializer\SerializerInterface;
use DateTimeImmutable;
use Generator;
use Traversable;

class FamilyAdapter implements FamilyAdapterInterface
{
    private int $batchSize = 100;

    private array $families = [];

    /** @var callable|null */
    private $responseCallback = null;

    public function __construct(
        private readonly FamilyApiInterface $familyApi,
        private readonly SerializerInterface $serializer
    ) {}

    /**
     * {@inheritDoc}
     */
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
        foreach ($this->familyApi->all($this->batchSize, $queryParameters->toArray()) as $family) {
            yield $this->serializer->denormalize($family, Family::class);
        }
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
        $this->families[] = $family;
        if (count($this->families) >= $this->batchSize) {
            $this->push();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function push(): void
    {
        if (! empty($this->families)) {
            $normalizedFamilies = $this->serializer->normalize($this->families);
            $response = $this->familyApi->upsertList($normalizedFamilies);
            $this->triggerResponseCallback($response, $this->families);
            $this->families = [];
        }
    }

    private function triggerResponseCallback(Traversable $response, array $pushedFamilies): void
    {
        if ($this->responseCallback !== null) {
            call_user_func($this->responseCallback, $response, $pushedFamilies, new DateTimeImmutable);
        }
    }
}
