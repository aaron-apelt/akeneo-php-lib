<?php

declare(strict_types=1);

use Akeneo\Pim\ApiClient\Api\AttributeOptionApiInterface;
use AkeneoLib\Adapter\AttributeOptionAdapter;
use AkeneoLib\Adapter\AttributeOptionAdapterInterface;
use AkeneoLib\Entity\AttributeOption;
use AkeneoLib\Search\QueryParameter;
use AkeneoLib\Serializer\SerializerInterface;

beforeEach(function () {
    $this->attributeOptionApi = mock(AttributeOptionApiInterface::class);
    $this->serializer = mock(SerializerInterface::class);
    $this->adapter = new AttributeOptionAdapter($this->attributeOptionApi, $this->serializer);
});

it('implements AttributeOptionAdapterInterface', function () {
    expect($this->adapter)->toBeInstanceOf(AttributeOptionAdapterInterface::class);
});

it('gets and sets batch size', function () {
    expect($this->adapter->getBatchSize())->toBe(100);
    $this->adapter->setBatchSize(25);
    expect($this->adapter->getBatchSize())->toBe(25);
});

it('gets and sets attribute code', function () {
    expect($this->adapter->getAttributeCode())->toBe('');
    $this->adapter->setAttributeCode('color');
    expect($this->adapter->getAttributeCode())->toBe('color');
});

it('registers a response callback', function () {
    $called = false;
    $cb = function () use (&$called) {
        $called = true;
    };
    $this->adapter->onResponse($cb);
    $this->adapter->setAttributeCode('size');

    $option = new AttributeOption('small');
    $this->serializer->shouldReceive('normalize')->andReturn([[]]);
    $this->attributeOptionApi->shouldReceive('upsertList')->andReturn(new ArrayIterator([]));
    $this->adapter->stage($option);
    $this->adapter->push();
    expect($called)->toBeTrue();
});

it('yields denormalized attribute options from all()', function () {
    $this->adapter->setAttributeCode('color');
    $apiOption = ['code' => 'red'];
    $optionObj = new AttributeOption('red');

    $cursor = resourceCursorMock([$apiOption]);
    $this->attributeOptionApi->shouldReceive('all')->andReturn($cursor);
    $this->serializer->shouldReceive('denormalize')->with($apiOption, AttributeOption::class)->andReturn($optionObj);

    $results = iterator_to_array($this->adapter->all());
    expect($results)->toHaveCount(1)->and($results[0])->toBe($optionObj);
});

it('uses query parameters in all()', function () {
    $this->adapter->setAttributeCode('size');
    $apiOption = ['code' => 'medium'];
    $optionObj = new AttributeOption('medium');
    $param = mock(QueryParameter::class);
    $param->shouldReceive('toArray')->andReturn(['limit' => 10]);
    $cursor = resourceCursorMock([$apiOption]);
    $this->attributeOptionApi->shouldReceive('all')->with('size', 100, ['limit' => 10])->andReturn($cursor);
    $this->serializer->shouldReceive('denormalize')->with($apiOption, AttributeOption::class)->andReturn($optionObj);

    $results = iterator_to_array($this->adapter->all($param));
    expect($results)->toHaveCount(1)->and($results[0])->toBe($optionObj);
});

it('gets and denormalizes an attribute option by code', function () {
    $this->adapter->setAttributeCode('color');
    $apiOption = ['code' => 'blue'];
    $optionObj = new AttributeOption('blue');
    $this->attributeOptionApi->shouldReceive('get')->with('color', 'blue')->andReturn($apiOption);
    $this->serializer->shouldReceive('denormalize')->with($apiOption, AttributeOption::class)->andReturn($optionObj);

    $result = $this->adapter->get('blue');
    expect($result)->toBe($optionObj);
});

it('stages attribute options and pushes when batch size is met', function () {
    $this->adapter->setBatchSize(2);
    $this->adapter->setAttributeCode('size');
    $o1 = new AttributeOption('small');
    $o2 = new AttributeOption('large');
    $this->serializer->shouldReceive('normalize')->once()->andReturn([[], []]);
    $this->attributeOptionApi->shouldReceive('upsertList')->once()->andReturn(new ArrayIterator([]));

    $this->adapter->stage($o1);
    $this->adapter->stage($o2);
    $this->adapter->push();
    expect(true)->toBeTrue();
});

it('pushes staged attribute options and clears the queue', function () {
    $this->adapter->setAttributeCode('color');
    $option = new AttributeOption('green');
    $this->serializer->shouldReceive('normalize')->once()->andReturn([[]]);
    $this->attributeOptionApi->shouldReceive('upsertList')->once()->andReturn(new ArrayIterator([]));
    $this->adapter->stage($option);
    $this->adapter->push();
    $this->adapter->push();
    expect(true)->toBeTrue();
});
