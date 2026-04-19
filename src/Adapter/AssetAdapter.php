<?php

declare(strict_types=1);

namespace AkeneoLib\Adapter;

use Akeneo\Pim\ApiClient\Api\AssetManager\AssetApiInterface;
use AkeneoLib\Adapter\Support\FluentAdapterResult;
use AkeneoLib\Entity\Asset;
use AkeneoLib\Search\QueryParameter;
use AkeneoLib\Serializer\SerializerInterface;
use Generator;

class AssetAdapter implements AssetAdapterInterface
{
    use BatchableAdapterTrait;

    private string $assetFamilyCode = '';

    public function __construct(
        private readonly AssetApiInterface $assetApi,
        private readonly SerializerInterface $serializer
    ) {}

    /**
     * {@inheritDoc}
     */
    public function getAssetFamilyCode(): string
    {
        return $this->assetFamilyCode;
    }

    /**
     * {@inheritDoc}
     */
    public function setAssetFamilyCode(string $code): self
    {
        $this->assetFamilyCode = $code;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function all(?QueryParameter $queryParameters = null): FluentAdapterResult
    {
        $queryParameters ??= new QueryParameter;
        $generator = function () use ($queryParameters): Generator {
            foreach ($this->assetApi->all($this->assetFamilyCode, $queryParameters->toArray()) as $asset) {
                yield $this->serializer->denormalize($asset, Asset::class, ['scopeName' => 'channel']);
            }
        };

        return new FluentAdapterResult($generator());
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $code): Asset
    {
        $asset = $this->assetApi->get($this->assetFamilyCode, $code);

        return $this->serializer->denormalize($asset, Asset::class, ['scopeName' => 'channel']);
    }

    /**
     * {@inheritDoc}
     */
    public function stage(Asset $asset): void
    {
        $this->addPendingItem($asset);
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
            $normalized = $this->serializer->normalize($this->pendingItems, ['scopeName' => 'channel']);
            $response = $this->assetApi->upsertList($this->assetFamilyCode, $normalized);
            $this->triggerResponseCallback($response, $this->pendingItems);
            $this->clearPendingItems();
        }
    }
}
