<?php

declare(strict_types=1);

use Akeneo\Pim\ApiClient\Api\AttributeApiInterface;
use AkeneoLib\Adapter\AttributeAdapter;
use AkeneoLib\Adapter\AttributeAdapterInterface;
use AkeneoLib\Entity\Attribute;
use AkeneoLib\Search\QueryParameter;
use AkeneoLib\Serializer\SerializerInterface;

beforeEach(function () {
    $this->attributeApi = mock(AttributeApiInterface::class);
    $this->serializer = mock(SerializerInterface::class);
    $this->adapter = new AttributeAdapter($this->attributeApi, $this->serializer);
});

it('implements AttributeAdapterInterface', function () {
    expect($this->adapter)->toBeInstanceOf(AttributeAdapterInterface::class);
});

it('gets and sets batch size', function () {
    expect($this->adapter->getBatchSize())->toBe(100);
    $this->adapter->setBatchSize(50);
    expect($this->adapter->getBatchSize())->toBe(50);
});

it('registers a response callback', function () {
    $called = false;
    $cb = function () use (&$called) {
        $called = true;
    };
    $this->adapter->onResponse($cb);

    $attribute = new Attribute('attr-1');
    $this->serializer->shouldReceive('normalize')->andReturn([[]]);
    $this->attributeApi->shouldReceive('upsertList')->andReturn(new ArrayIterator([]));
    $this->adapter->stage($attribute);
    $this->adapter->push();
    expect($called)->toBeTrue();
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
    $param->shouldReceive('toArray')->andReturn(['search' => 'test']);
    $cursor = resourceCursorMock([$apiAttribute]);
    $this->attributeApi->shouldReceive('all')->with(100, ['search' => 'test'])->andReturn($cursor);
    $this->serializer->shouldReceive('denormalize')->with($apiAttribute, Attribute::class)->andReturn($attributeObj);

    $results = iterator_to_array($this->adapter->all($param));
    expect($results)->toHaveCount(1)->and($results[0])->toBe($attributeObj);
});

it('gets and denormalizes an attribute by code', function () {
    $apiAttribute = ['code' => 'attr-3'];
    $attributeObj = new Attribute('attr-3');
    $this->attributeApi->shouldReceive('get')->with('attr-3')->andReturn($apiAttribute);
    $this->serializer->shouldReceive('denormalize')->with($apiAttribute, Attribute::class)->andReturn($attributeObj);

    $result = $this->adapter->get('attr-3');
    expect($result)->toBe($attributeObj);
});

it('stages attributes and pushes when batch size is met', function () {
    $this->adapter->setBatchSize(2);
    $a1 = new Attribute('attr-a');
    $a2 = new Attribute('attr-b');
    $this->serializer->shouldReceive('normalize')->once()->andReturn([[], []]);
    $this->attributeApi->shouldReceive('upsertList')->once()->andReturn(new ArrayIterator([]));

    $this->adapter->stage($a1);
    $this->adapter->stage($a2);
    $this->adapter->push();
    expect(true)->toBeTrue();
});

it('pushes staged attributes and clears the queue', function () {
    $attribute = new Attribute('attr-x');
    $this->serializer->shouldReceive('normalize')->once()->andReturn([[]]);
    $this->attributeApi->shouldReceive('upsertList')->once()->andReturn(new ArrayIterator([]));
    $this->adapter->stage($attribute);
    $this->adapter->push();
    $this->adapter->push();
    expect(true)->toBeTrue();
});
