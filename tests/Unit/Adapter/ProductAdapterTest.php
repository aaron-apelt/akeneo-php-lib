<?php

declare(strict_types=1);

use Akeneo\Pim\ApiClient\Api\ProductApiInterface;
use AkeneoLib\Adapter\ProductAdapter;
use AkeneoLib\Adapter\ProductAdapterInterface;
use AkeneoLib\Entity\Product;
use AkeneoLib\Search\QueryParameter;
use AkeneoLib\Serializer\SerializerInterface;

beforeEach(function () {
    $this->productApi = mock(ProductApiInterface::class);
    $this->serializer = mock(SerializerInterface::class);
    $this->adapter = new ProductAdapter($this->productApi, $this->serializer);
});

it('implements ProductAdapterInterface', function () {
    expect($this->adapter)->toBeInstanceOf(ProductAdapterInterface::class);
});

it('gets and sets batch size', function () {
    expect($this->adapter->getBatchSize())->toBe(100);
    $this->adapter->setBatchSize(42);
    expect($this->adapter->getBatchSize())->toBe(42);
});

it('registers a response callback', function () {
    $called = false;
    $cb = function () use (&$called) {
        $called = true;
    };
    $this->adapter->onResponse($cb);

    $product = new Product('sku-1');
    $this->serializer->shouldReceive('normalize')->andReturn([[]]);
    $this->productApi->shouldReceive('upsertList')->andReturn(new ArrayIterator([]));
    $this->adapter->stage($product);
    $this->adapter->push();
    expect($called)->toBeTrue();
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

    $result = $this->adapter->get('sku-3');
    expect($result)->toBe($productObj);
});

it('stages products and pushes when batch size is met', function () {
    $this->adapter->setBatchSize(2);
    $p1 = new Product('sku-a');
    $p2 = new Product('sku-b');
    $this->serializer->shouldReceive('normalize')->once()->andReturn([[], []]);
    $this->productApi->shouldReceive('upsertList')->once()->andReturn(new ArrayIterator([]));

    $this->adapter->stage($p1);
    $this->adapter->stage($p2);
    $this->adapter->push();
    expect(true)->toBeTrue();
});

it('pushes staged products and clears the queue', function () {
    $product = new Product('sku-x');
    $this->serializer->shouldReceive('normalize')->once()->andReturn([[]]);
    $this->productApi->shouldReceive('upsertList')->once()->andReturn(new ArrayIterator([]));
    $this->adapter->stage($product);
    $this->adapter->push();
    $this->adapter->push();
    expect(true)->toBeTrue();
});
