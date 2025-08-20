<?php

declare(strict_types=1);

namespace AkeneoLib\Adapter;

use Akeneo\Pim\ApiClient\Api\AttributeGroupApiInterface;
use AkeneoLib\Entity\AttributeGroup;
use AkeneoLib\Search\QueryParameter;
use AkeneoLib\Serializer\SerializerInterface;
use DateTimeImmutable;
use Generator;
use Traversable;

class AttributeGroupAdapter implements AttributeGroupAdapterInterface
{
    private int $batchSize = 100;

    private array $attributeGroups = [];

    /** @var callable|null */
    private $responseCallback = null;

    public function __construct(
        private readonly AttributeGroupApiInterface $attributeGroupApi,
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
        foreach ($this->attributeGroupApi->all($this->batchSize, $queryParameters->toArray()) as $group) {
            yield $this->serializer->denormalize($group, AttributeGroup::class);
        }
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
        $this->attributeGroups[] = $attributeGroup;
        if (count($this->attributeGroups) >= $this->batchSize) {
            $this->push();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function push(): void
    {
        if (! empty($this->attributeGroups)) {
            $normalized = $this->serializer->normalize($this->attributeGroups);
            $response = $this->attributeGroupApi->upsertList($normalized);
            $this->triggerResponseCallback($response, $this->attributeGroups);
            $this->attributeGroups = [];
        }
    }

    private function triggerResponseCallback(Traversable $response, array $pushedItems): void
    {
        if ($this->responseCallback !== null) {
            call_user_func($this->responseCallback, $response, $pushedItems, new DateTimeImmutable);
        }
    }
}
