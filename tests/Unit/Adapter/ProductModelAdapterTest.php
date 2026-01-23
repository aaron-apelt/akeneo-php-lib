<?php

declare(strict_types=1);

use Akeneo\Pim\ApiClient\Api\ProductModelApiInterface;
use AkeneoLib\Adapter\ProductModelAdapter;
use AkeneoLib\Adapter\ProductModelAdapterInterface;
use AkeneoLib\Entity\ProductModel;
use AkeneoLib\Search\QueryParameter;
use AkeneoLib\Serializer\SerializerInterface;

beforeEach(function () {
    $this->productModelApi = mock(ProductModelApiInterface::class);
    $this->serializer = mock(SerializerInterface::class);
    $this->adapter = new ProductModelAdapter($this->productModelApi, $this->serializer);
});

it('implements ProductModelAdapterInterface', function () {
    expect($this->adapter)->toBeInstanceOf(ProductModelAdapterInterface::class);
});

it('gets and sets batch size', function () {
    expect($this->adapter->getBatchSize())->toBe(100);
    $this->adapter->setBatchSize(75);
    expect($this->adapter->getBatchSize())->toBe(75);
});

it('registers a response callback', function () {
    $called = false;
    $cb = function () use (&$called) {
        $called = true;
    };
    $this->adapter->onResponse($cb);

    $model = new ProductModel('model-1');
    $this->serializer->shouldReceive('normalize')->andReturn([[]]);
    $this->productModelApi->shouldReceive('upsertList')->andReturn(new ArrayIterator([]));
    $this->adapter->stage($model);
    $this->adapter->push();
    expect($called)->toBeTrue();
});

it('yields denormalized product models from all()', function () {
    $apiModel = ['code' => 'model-1'];
    $modelObj = new ProductModel('model-1');

    $cursor = resourceCursorMock([$apiModel]);
    $this->productModelApi->shouldReceive('all')->andReturn($cursor);
    $this->serializer->shouldReceive('denormalize')->with($apiModel, ProductModel::class)->andReturn($modelObj);

    $results = iterator_to_array($this->adapter->all());
    expect($results)->toHaveCount(1)->and($results[0])->toBe($modelObj);
});

it('uses query parameters in all()', function () {
    $apiModel = ['code' => 'model-2'];
    $modelObj = new ProductModel('model-2');
    $param = mock(QueryParameter::class);
    $param->shouldReceive('toArray')->andReturn(['family' => 'test']);
    $cursor = resourceCursorMock([$apiModel]);
    $this->productModelApi->shouldReceive('all')->with(100, ['family' => 'test'])->andReturn($cursor);
    $this->serializer->shouldReceive('denormalize')->with($apiModel, ProductModel::class)->andReturn($modelObj);

    $results = iterator_to_array($this->adapter->all($param));
    expect($results)->toHaveCount(1)->and($results[0])->toBe($modelObj);
});

it('gets and denormalizes a product model by code', function () {
    $apiModel = ['code' => 'model-3'];
    $modelObj = new ProductModel('model-3');
    $this->productModelApi->shouldReceive('get')->with('model-3')->andReturn($apiModel);
    $this->serializer->shouldReceive('denormalize')->with($apiModel, ProductModel::class)->andReturn($modelObj);

    $result = $this->adapter->get('model-3');
    expect($result)->toBe($modelObj);
});

it('stages product models and pushes when batch size is met', function () {
    $this->adapter->setBatchSize(2);
    $m1 = new ProductModel('model-a');
    $m2 = new ProductModel('model-b');
    $this->serializer->shouldReceive('normalize')->once()->andReturn([[], []]);
    $this->productModelApi->shouldReceive('upsertList')->once()->andReturn(new ArrayIterator([]));

    $this->adapter->stage($m1);
    $this->adapter->stage($m2);
    $this->adapter->push();
    expect(true)->toBeTrue();
});

it('pushes staged product models and clears the queue', function () {
    $model = new ProductModel('model-x');
    $this->serializer->shouldReceive('normalize')->once()->andReturn([[]]);
    $this->productModelApi->shouldReceive('upsertList')->once()->andReturn(new ArrayIterator([]));
    $this->adapter->stage($model);
    $this->adapter->push();
    $this->adapter->push();
    expect(true)->toBeTrue();
});
