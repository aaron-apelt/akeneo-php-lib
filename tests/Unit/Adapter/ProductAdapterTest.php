<?php

declare(strict_types=1);

use Akeneo\Pim\ApiClient\Api\ProductApiInterface;
use AkeneoLib\Adapter\ProductAdapter;
use AkeneoLib\Entity\Product;
use AkeneoLib\Search\QueryParameter;
use AkeneoLib\Serializer\SerializerInterface;

beforeEach(function () {
    $this->productApi = mock(ProductApiInterface::class);
    $this->serializer = mock(SerializerInterface::class);
    $this->adapter = new ProductAdapter($this->productApi, $this->serializer);
});

it('yields denormalized products from all()', function () {
    $apiProduct = ['identifier' => 'sku-1'];
    $productObj = new Product('sku-1');

    $cursor = resourceCursorMock([$apiProduct]);
    $this->productApi->shouldReceive('all')->andReturn($cursor);
    $this->serializer->shouldReceive('denormalize')->with($apiProduct, Product::class)->andReturn($productObj);

    $results = iterator_to_array($this->adapter->all());
    expect($results)->toHaveCount(1)->and($results[0])->toBe($productObj);
});

it('uses query parameters in all()', function () {
    $apiProduct = ['identifier' => 'sku-2'];
    $productObj = new Product('sku-2');
    $param = mock(QueryParameter::class);
    $param->shouldReceive('toArray')->andReturn(['foo' => 'bar']);
    $cursor = resourceCursorMock([$apiProduct]);
    $this->productApi->shouldReceive('all')->with(100, ['foo' => 'bar'])->andReturn($cursor);
    $this->serializer->shouldReceive('denormalize')->with($apiProduct, Product::class)->andReturn($productObj);

    $results = iterator_to_array($this->adapter->all($param));
    expect($results)->toHaveCount(1)->and($results[0])->toBe($productObj);
});

it('gets and denormalizes a product by identifier', function () {
    $apiProduct = ['identifier' => 'sku-3'];
    $productObj = new Product('sku-3');
    $this->productApi->shouldReceive('get')->with('sku-3')->andReturn($apiProduct);
    $this->serializer->shouldReceive('denormalize')->with($apiProduct, Product::class)->andReturn($productObj);

    expect($this->adapter->get('sku-3'))->toBe($productObj);
});
