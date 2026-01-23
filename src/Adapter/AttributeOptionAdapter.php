<?php

declare(strict_types=1);

namespace AkeneoLib\Adapter;

use Akeneo\Pim\ApiClient\Api\AttributeOptionApiInterface;
use AkeneoLib\Adapter\Support\FluentAdapterResult;
use AkeneoLib\Entity\AttributeOption;
use AkeneoLib\Search\QueryParameter;
use AkeneoLib\Serializer\SerializerInterface;
use DateTimeImmutable;
use Generator;
use Traversable;

class AttributeOptionAdapter implements AttributeOptionAdapterInterface
{
    private int $batchSize = 100;

    private string $attributeCode = '';

    private array $options = [];

    /** @var callable|null */
    private $responseCallback = null;

    public function __construct(
        private readonly AttributeOptionApiInterface $attributeOptionApi,
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
        $this->options[] = $option;
        if (count($this->options) >= $this->batchSize) {
            $this->push();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function push(): void
    {
        if (! empty($this->options)) {
            $normalized = $this->serializer->normalize($this->options);
            $response = $this->attributeOptionApi->upsertList($this->attributeCode, $normalized);
            $this->triggerResponseCallback($response, $this->options);
            $this->options = [];
        }
    }

    private function triggerResponseCallback(Traversable $response, array $pushedItems): void
    {
        if ($this->responseCallback !== null) {
            call_user_func($this->responseCallback, $response, $pushedItems, new DateTimeImmutable);
        }
    }
}
