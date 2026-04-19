<?php

declare(strict_types=1);

use Akeneo\Pim\ApiClient\Api\AttributeApiInterface;
use AkeneoLib\Adapter\AttributeAdapter;
use AkeneoLib\Entity\Attribute;
use AkeneoLib\Search\QueryParameter;
use AkeneoLib\Serializer\SerializerInterface;

beforeEach(function () {
    $this->attributeApi = mock(AttributeApiInterface::class);
    $this->serializer = mock(SerializerInterface::class);
    $this->adapter = new AttributeAdapter($this->attributeApi, $this->serializer);
});

it('yields denormalized attributes from all()', function () {
    $apiAttribute = ['code' => 'attr-1'];
    $attributeObj = new Attribute('attr-1');

    $cursor = resourceCursorMock([$apiAttribute]);
    $this->attributeApi->shouldReceive('all')->andReturn($cursor);
    $this->serializer->shouldReceive('denormalize')->with($apiAttribute, Attribute::class)->andReturn($attributeObj);

    $results = iterator_to_array($this->adapter->all());
    expect($results)->toHaveCount(1)->and($results[0])->toBe($attributeObj);
});

it('uses query parameters in all()', function () {
    $apiAttribute = ['code' => 'attr-2'];
    $attributeObj = new Attribute('attr-2');
    $param = mock(QueryParameter::class);
    $param->shouldReceive('toArray')->andReturn(['type' => 'pim_catalog_text']);
    $cursor = resourceCursorMock([$apiAttribute]);
    $this->attributeApi->shouldReceive('all')->with(100, ['type' => 'pim_catalog_text'])->andReturn($cursor);
    $this->serializer->shouldReceive('denormalize')->with($apiAttribute, Attribute::class)->andReturn($attributeObj);

    $results = iterator_to_array($this->adapter->all($param));
    expect($results)->toHaveCount(1)->and($results[0])->toBe($attributeObj);
});

it('gets and denormalizes an attribute by code', function () {
    $apiAttribute = ['code' => 'attr-3'];
    $attributeObj = new Attribute('attr-3');
    $this->attributeApi->shouldReceive('get')->with('attr-3')->andReturn($apiAttribute);
    $this->serializer->shouldReceive('denormalize')->with($apiAttribute, Attribute::class)->andReturn($attributeObj);

    expect($this->adapter->get('attr-3'))->toBe($attributeObj);
});
