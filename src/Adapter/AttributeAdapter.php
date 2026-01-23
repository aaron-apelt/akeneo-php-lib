<?php

declare(strict_types=1);

namespace AkeneoLib\Adapter;

use Akeneo\Pim\ApiClient\Api\AttributeApiInterface;
use AkeneoLib\Adapter\Support\FluentAdapterResult;
use AkeneoLib\Entity\Attribute;
use AkeneoLib\Search\QueryParameter;
use AkeneoLib\Serializer\SerializerInterface;
use DateTimeImmutable;
use Generator;
use Traversable;

class AttributeAdapter implements AttributeAdapterInterface
{
    private int $batchSize = 100;

    private array $attributes = [];

    /** @var callable|null */
    private $responseCallback = null;

    public function __construct(
        private readonly AttributeApiInterface $attributeApi,
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
        $this->attributes[] = $attribute;
        if (count($this->attributes) >= $this->batchSize) {
            $this->push();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function push(): void
    {
        if (! empty($this->attributes)) {
            $normalizedAttributes = $this->serializer->normalize($this->attributes);
            $response = $this->attributeApi->upsertList($normalizedAttributes);
            $this->triggerResponseCallback($response, $this->attributes);
            $this->attributes = [];
        }
    }

    private function triggerResponseCallback(Traversable $response, array $pushedAttributes): void
    {
        if ($this->responseCallback !== null) {
            call_user_func($this->responseCallback, $response, $pushedAttributes, new DateTimeImmutable);
        }
    }
}
