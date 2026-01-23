<?php

declare(strict_types=1);

use Akeneo\Pim\ApiClient\Api\CategoryApiInterface;
use AkeneoLib\Adapter\CategoryAdapter;
use AkeneoLib\Adapter\CategoryAdapterInterface;
use AkeneoLib\Entity\Category;
use AkeneoLib\Search\QueryParameter;
use AkeneoLib\Serializer\SerializerInterface;

beforeEach(function () {
    $this->categoryApi = mock(CategoryApiInterface::class);
    $this->serializer = mock(SerializerInterface::class);
    $this->adapter = new CategoryAdapter($this->categoryApi, $this->serializer);
});

it('implements CategoryAdapterInterface', function () {
    expect($this->adapter)->toBeInstanceOf(CategoryAdapterInterface::class);
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

    $category = new Category('cat-1');
    $this->serializer->shouldReceive('normalize')->andReturn([[]]);
    $this->categoryApi->shouldReceive('upsertList')->andReturn(new ArrayIterator([]));
    $this->adapter->stage($category);
    $this->adapter->push();
    expect($called)->toBeTrue();
});

it('yields denormalized categories from all()', function () {
    $apiCategory = ['code' => 'cat-1'];
    $categoryObj = new Category('cat-1');

    $cursor = resourceCursorMock([$apiCategory]);
    $this->categoryApi->shouldReceive('all')->andReturn($cursor);
    $this->serializer->shouldReceive('denormalize')->with($apiCategory, Category::class)->andReturn($categoryObj);

    $results = iterator_to_array($this->adapter->all());
    expect($results)->toHaveCount(1)->and($results[0])->toBe($categoryObj);
});

it('uses query parameters in all()', function () {
    $apiCategory = ['code' => 'cat-2'];
    $categoryObj = new Category('cat-2');
    $param = mock(QueryParameter::class);
    $param->shouldReceive('toArray')->andReturn(['foo' => 'bar']);
    $cursor = resourceCursorMock([$apiCategory]);
    $this->categoryApi->shouldReceive('all')->with(100, ['foo' => 'bar'])->andReturn($cursor);
    $this->serializer->shouldReceive('denormalize')->with($apiCategory, Category::class)->andReturn($categoryObj);

    $results = iterator_to_array($this->adapter->all($param));
    expect($results)->toHaveCount(1)->and($results[0])->toBe($categoryObj);
});

it('gets and denormalizes a category by code', function () {
    $apiCategory = ['code' => 'cat-3'];
    $categoryObj = new Category('cat-3');
    $this->categoryApi->shouldReceive('get')->with('cat-3')->andReturn($apiCategory);
    $this->serializer->shouldReceive('denormalize')->with($apiCategory, Category::class)->andReturn($categoryObj);

    $result = $this->adapter->get('cat-3');
    expect($result)->toBe($categoryObj);
});

it('stages categories and pushes when batch size is met', function () {
    $this->adapter->setBatchSize(2);
    $c1 = new Category('cat-a');
    $c2 = new Category('cat-b');
    $this->serializer->shouldReceive('normalize')->once()->andReturn([[], []]);
    $this->categoryApi->shouldReceive('upsertList')->once()->andReturn(new ArrayIterator([]));

    $this->adapter->stage($c1);
    $this->adapter->stage($c2);
    $this->adapter->push();
    expect(true)->toBeTrue();
});

it('pushes staged categories and clears the queue', function () {
    $category = new Category('cat-x');
    $this->serializer->shouldReceive('normalize')->once()->andReturn([[]]);
    $this->categoryApi->shouldReceive('upsertList')->once()->andReturn(new ArrayIterator([]));
    $this->adapter->stage($category);
    $this->adapter->push();
    $this->adapter->push();
    expect(true)->toBeTrue();
});
