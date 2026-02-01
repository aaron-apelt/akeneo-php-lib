<?php

declare(strict_types=1);

namespace AkeneoLib\Adapter;

use Akeneo\Pim\ApiClient\Api\AssetManager\AssetMediaFileApiInterface;
use Psr\Http\Message\ResponseInterface;

readonly class AssetMediaFileAdapter implements AssetMediaFileAdapterInterface
{
    public function __construct(
        private AssetMediaFileApiInterface $assetMediaFileApi
    ) {}

    /**
     * {@inheritDoc}
     */
    public function download(string $code): ResponseInterface
    {
        return $this->assetMediaFileApi->download($code);
    }

    /**
     * {@inheritDoc}
     */
    public function create($mediaFile): string
    {
        return $this->assetMediaFileApi->create($mediaFile);
    }
}
