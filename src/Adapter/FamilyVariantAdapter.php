<?php

declare(strict_types=1);

namespace AkeneoLib\Adapter;

use Akeneo\Pim\ApiClient\Api\FamilyVariantApiInterface;
use AkeneoLib\Adapter\Support\FluentAdapterResult;
use AkeneoLib\Entity\FamilyVariant;
use AkeneoLib\Search\QueryParameter;
use AkeneoLib\Serializer\SerializerInterface;
use DateTimeImmutable;
use Generator;
use Traversable;

class FamilyVariantAdapter implements FamilyVariantAdapterInterface
{
    private int $batchSize = 100;

    private array $familyVariants = [];

    /** @var callable|null */
    private $responseCallback = null;

    public function __construct(
        private readonly FamilyVariantApiInterface $familyVariantApi,
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
    public function all(string $familyCode, ?QueryParameter $queryParameters = null): FluentAdapterResult
    {
        $queryParameters ??= new QueryParameter;
        $generator = function () use ($familyCode, $queryParameters): Generator {
            foreach ($this->familyVariantApi->all($familyCode, $this->batchSize, $queryParameters->toArray()) as $variant) {
                yield $this->serializer->denormalize($variant, FamilyVariant::class);
            }
        };

        return new FluentAdapterResult($generator());
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $familyCode, string $code): FamilyVariant
    {
        $variant = $this->familyVariantApi->get($familyCode, $code);

        return $this->serializer->denormalize($variant, FamilyVariant::class);
    }

    /**
     * {@inheritDoc}
     */
    public function stage(FamilyVariant $familyVariant): void
    {
        $this->familyVariants[] = $familyVariant;
        if (count($this->familyVariants) >= $this->batchSize) {
            $this->push($familyVariant->getFamily());
        }
    }

    /**
     * {@inheritDoc}
     */
    public function push(string $familyCode): void
    {
        if (! empty($this->familyVariants)) {
            $normalized = $this->serializer->normalize($this->familyVariants);
            $response = $this->familyVariantApi->upsertList($familyCode, $normalized);
            $this->triggerResponseCallback($response, $this->familyVariants);
            $this->familyVariants = [];
        }
    }

    private function triggerResponseCallback(Traversable $response, array $pushedItems): void
    {
        if ($this->responseCallback !== null) {
            call_user_func($this->responseCallback, $response, $pushedItems, new DateTimeImmutable);
        }
    }
}
