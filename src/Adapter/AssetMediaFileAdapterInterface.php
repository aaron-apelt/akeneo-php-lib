<?php

declare(strict_types=1);

namespace AkeneoLib\Adapter;

use Akeneo\Pim\ApiClient\Exception\HttpException;
use Akeneo\Pim\ApiClient\Exception\RuntimeException;
use Psr\Http\Message\ResponseInterface;

interface AssetMediaFileAdapterInterface
{
    /**
     * Downloads an asset media file by its code.
     *
     * @throws HttpException
     */
    public function download(string $code): ResponseInterface;

    /**
     * Creates a new asset media file and returns the code of the created media file.
     *
     * @param  string|resource  $mediaFile  File path or resource of the media file
     *
     * @throws HttpException
     * @throws RuntimeException
     */
    public function create($mediaFile): string;
}
