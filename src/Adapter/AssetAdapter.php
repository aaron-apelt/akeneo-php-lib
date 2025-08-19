<?php

declare(strict_types=1);

namespace AkeneoLib\Adapter;

use Akeneo\Pim\ApiClient\Api\AssetManager\AssetApiInterface;
use AkeneoLib\Entity\Asset;
use AkeneoLib\Search\QueryParameter;
use AkeneoLib\Serializer\SerializerInterface;
use DateTimeImmutable;
use Generator;

class AssetAdapter implements AssetAdapterInterface
{
    private int $batchSize = 100;

    private string $assetFamilyCode = '';

    private array $assets = [];

    /** @var callable|null */
    private $responseCallback = null;

    public function __construct(
        private readonly AssetApiInterface $assetApi,
        private readonly SerializerInterface $serializer
    ) {}

    /**
     * {@inheritDoc}
     */
    public function onResponse(callable $callback): AssetAdapterInterface
    {
        $this->responseCallback = $callback;

        return $this;
    }

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
    public function setAssetFamilyCode(string $code): AssetAdapterInterface
    {
        $this->assetFamilyCode = $code;

        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function all(?QueryParameter $queryParameters = null): Generator
    {
        $queryParameters ??= new QueryParameter;
        foreach ($this->assetApi->all($this->assetFamilyCode, $queryParameters->toArray()) as $asset) {
            yield $this->serializer->denormalize($asset, Asset::class, ['scopeName' => 'channel']);
        }
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
        $this->assets[] = $asset;
        if (count($this->assets) >= $this->batchSize) {
            $this->push();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function push(): void
    {
        if (! empty($this->assets)) {
            $normalizedAssets = $this->serializer->normalize($this->assets, ['scopeName' => 'channel']);
            $response = $this->assetApi->upsertList($this->assetFamilyCode, $normalizedAssets);
            $this->triggerResponseCallback($response, $this->assets);
            $this->assets = [];
        }
    }

    private function triggerResponseCallback(array $response, array $pushedAssets): void
    {
        if ($this->responseCallback !== null) {
            call_user_func($this->responseCallback, $response, $pushedAssets, new DateTimeImmutable);
        }
    }
}
