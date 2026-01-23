<?php

declare(strict_types=1);

use Akeneo\Pim\ApiClient\Api\AssetManager\AssetApiInterface;
use AkeneoLib\Adapter\AssetAdapter;
use AkeneoLib\Adapter\AssetAdapterInterface;
use AkeneoLib\Entity\Asset;
use AkeneoLib\Search\QueryParameter;
use AkeneoLib\Serializer\SerializerInterface;

beforeEach(function () {
    $this->assetApi = mock(AssetApiInterface::class);
    $this->serializer = mock(SerializerInterface::class);
    $this->adapter = new AssetAdapter($this->assetApi, $this->serializer);
});

it('implements AssetAdapterInterface', function () {
    expect($this->adapter)->toBeInstanceOf(AssetAdapterInterface::class);
});

it('gets and sets batch size', function () {
    expect($this->adapter->getBatchSize())->toBe(100);
    $this->adapter->setBatchSize(50);
    expect($this->adapter->getBatchSize())->toBe(50);
});

it('gets and sets asset family code', function () {
    expect($this->adapter->getAssetFamilyCode())->toBe('');
    $this->adapter->setAssetFamilyCode('product_images');
    expect($this->adapter->getAssetFamilyCode())->toBe('product_images');
});

it('registers a response callback', function () {
    $called = false;
    $cb = function () use (&$called) {
        $called = true;
    };
    $this->adapter->onResponse($cb);
    $this->adapter->setAssetFamilyCode('images');

    $asset = new Asset('img-001');
    $this->serializer->shouldReceive('normalize')->andReturn([[]]);
    $this->assetApi->shouldReceive('upsertList')->andReturn([]);
    $this->adapter->stage($asset);
    $this->adapter->push();
    expect($called)->toBeTrue();
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

    $result = $this->adapter->get('img-003');
    expect($result)->toBe($assetObj);
});

it('stages assets and pushes when batch size is met', function () {
    $this->adapter->setBatchSize(2);
    $this->adapter->setAssetFamilyCode('images');
    $a1 = new Asset('img-a');
    $a2 = new Asset('img-b');
    $this->serializer->shouldReceive('normalize')->once()->andReturn([[], []]);
    $this->assetApi->shouldReceive('upsertList')->once()->andReturn([]);

    $this->adapter->stage($a1);
    $this->adapter->stage($a2);
    $this->adapter->push();
    expect(true)->toBeTrue();
});

it('pushes staged assets and clears the queue', function () {
    $this->adapter->setAssetFamilyCode('images');
    $asset = new Asset('img-x');
    $this->serializer->shouldReceive('normalize')->once()->andReturn([[]]);
    $this->assetApi->shouldReceive('upsertList')->once()->andReturn([]);
    $this->adapter->stage($asset);
    $this->adapter->push();
    $this->adapter->push();
    expect(true)->toBeTrue();
});
