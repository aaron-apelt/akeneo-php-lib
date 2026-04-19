<?php

declare(strict_types=1);

use Akeneo\Pim\ApiClient\Api\ProductModelApiInterface;
use AkeneoLib\Adapter\ProductModelAdapter;
use AkeneoLib\Entity\ProductModel;
use AkeneoLib\Search\QueryParameter;
use AkeneoLib\Serializer\SerializerInterface;

beforeEach(function () {
    $this->productModelApi = mock(ProductModelApiInterface::class);
    $this->serializer = mock(SerializerInterface::class);
    $this->adapter = new ProductModelAdapter($this->productModelApi, $this->serializer);
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
    $param->shouldReceive('toArray')->andReturn(['family' => 'clothing']);
    $cursor = resourceCursorMock([$apiModel]);
    $this->productModelApi->shouldReceive('all')->with(100, ['family' => 'clothing'])->andReturn($cursor);
    $this->serializer->shouldReceive('denormalize')->with($apiModel, ProductModel::class)->andReturn($modelObj);

    $results = iterator_to_array($this->adapter->all($param));
    expect($results)->toHaveCount(1)->and($results[0])->toBe($modelObj);
});

it('gets and denormalizes a product model by code', function () {
    $apiModel = ['code' => 'model-3'];
    $modelObj = new ProductModel('model-3');
    $this->productModelApi->shouldReceive('get')->with('model-3')->andReturn($apiModel);
    $this->serializer->shouldReceive('denormalize')->with($apiModel, ProductModel::class)->andReturn($modelObj);

    expect($this->adapter->get('model-3'))->toBe($modelObj);
});
