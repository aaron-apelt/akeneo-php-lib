<?php

declare(strict_types=1);

use Akeneo\Pim\ApiClient\Api\CategoryApiInterface;
use AkeneoLib\Adapter\CategoryAdapter;
use AkeneoLib\Entity\Category;
use AkeneoLib\Search\QueryParameter;
use AkeneoLib\Serializer\SerializerInterface;

beforeEach(function () {
    $this->categoryApi = mock(CategoryApiInterface::class);
    $this->serializer = mock(SerializerInterface::class);
    $this->adapter = new CategoryAdapter($this->categoryApi, $this->serializer);
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

    expect($this->adapter->get('cat-3'))->toBe($categoryObj);
});
