<?php

declare(strict_types=1);

use Akeneo\Pim\ApiClient\Api\AssetManager\AssetMediaFileApiInterface;
use AkeneoLib\Adapter\AssetMediaFileAdapter;
use AkeneoLib\Adapter\AssetMediaFileAdapterInterface;
use Psr\Http\Message\ResponseInterface;

beforeEach(function () {
    $this->assetMediaFileApi = mock(AssetMediaFileApiInterface::class);
    $this->adapter = new AssetMediaFileAdapter($this->assetMediaFileApi);
});

it('implements AssetMediaFileAdapterInterface', function () {
    expect($this->adapter)->toBeInstanceOf(AssetMediaFileAdapterInterface::class);
});

it('downloads a media file by code', function () {
    $response = mock(ResponseInterface::class);
    $this->assetMediaFileApi->shouldReceive('download')->with('media-123')->andReturn($response);

    $result = $this->adapter->download('media-123');
    expect($result)->toBe($response);
});

it('creates a media file from file path', function () {
    $filePath = '/path/to/image.jpg';
    $this->assetMediaFileApi->shouldReceive('create')->with($filePath)->andReturn('media-456');

    $result = $this->adapter->create($filePath);
    expect($result)->toBe('media-456');
});

it('creates a media file from resource', function () {
    $resource = fopen('php://memory', 'r');
    $this->assetMediaFileApi->shouldReceive('create')->with($resource)->andReturn('media-789');

    $result = $this->adapter->create($resource);
    expect($result)->toBe('media-789');

    fclose($resource);
});
