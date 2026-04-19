<?php

declare(strict_types=1);

use Akeneo\Pim\ApiClient\Api\AssetManager\AssetApiInterface;
use AkeneoLib\Adapter\AssetAdapter;
use AkeneoLib\Entity\Asset;
use AkeneoLib\Search\QueryParameter;
use AkeneoLib\Serializer\SerializerInterface;

beforeEach(function () {
    $this->assetApi = mock(AssetApiInterface::class);
    $this->serializer = mock(SerializerInterface::class);
    $this->adapter = new AssetAdapter($this->assetApi, $this->serializer);
});

it('gets and sets asset family code', function () {
    expect($this->adapter->getAssetFamilyCode())->toBe('');
    $this->adapter->setAssetFamilyCode('product_images');
    expect($this->adapter->getAssetFamilyCode())->toBe('product_images');
});

it('yields denormalized assets from all()', function () {
    $this->adapter->setAssetFamilyCode('images');
    $apiAsset = ['code' => 'img-001'];
    $assetObj = new Asset('img-001');

    $cursor = resourceCursorMock([$apiAsset]);
    $this->assetApi->shouldReceive('all')->andReturn($cursor);
    $this->serializer->shouldReceive('denormalize')->with($apiAsset, Asset::class, ['scopeName' => 'channel'])->andReturn($assetObj);

    $results = iterator_to_array($this->adapter->all());
    expect($results)->toHaveCount(1)->and($results[0])->toBe($assetObj);
});

it('uses query parameters in all()', function () {
    $this->adapter->setAssetFamilyCode('images');
    $apiAsset = ['code' => 'img-002'];
    $assetObj = new Asset('img-002');
    $param = mock(QueryParameter::class);
    $param->shouldReceive('toArray')->andReturn(['limit' => 5]);
    $cursor = resourceCursorMock([$apiAsset]);
    $this->assetApi->shouldReceive('all')->with('images', ['limit' => 5])->andReturn($cursor);
    $this->serializer->shouldReceive('denormalize')->with($apiAsset, Asset::class, ['scopeName' => 'channel'])->andReturn($assetObj);

    $results = iterator_to_array($this->adapter->all($param));
    expect($results)->toHaveCount(1)->and($results[0])->toBe($assetObj);
});

it('gets and denormalizes an asset by code', function () {
    $this->adapter->setAssetFamilyCode('images');
    $apiAsset = ['code' => 'img-003'];
    $assetObj = new Asset('img-003');
    $this->assetApi->shouldReceive('get')->with('images', 'img-003')->andReturn($apiAsset);
    $this->serializer->shouldReceive('denormalize')->with($apiAsset, Asset::class, ['scopeName' => 'channel'])->andReturn($assetObj);

    expect($this->adapter->get('img-003'))->toBe($assetObj);
});
