<?php

declare(strict_types=1);

use Akeneo\Pim\ApiClient\Api\AttributeOptionApiInterface;
use AkeneoLib\Adapter\AttributeOptionAdapter;
use AkeneoLib\Entity\AttributeOption;
use AkeneoLib\Search\QueryParameter;
use AkeneoLib\Serializer\SerializerInterface;

beforeEach(function () {
    $this->attributeOptionApi = mock(AttributeOptionApiInterface::class);
    $this->serializer = mock(SerializerInterface::class);
    $this->adapter = new AttributeOptionAdapter($this->attributeOptionApi, $this->serializer);
});

it('gets and sets attribute code', function () {
    expect($this->adapter->getAttributeCode())->toBe('');
    $this->adapter->setAttributeCode('color');
    expect($this->adapter->getAttributeCode())->toBe('color');
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

    expect($this->adapter->get('blue'))->toBe($optionObj);
});
