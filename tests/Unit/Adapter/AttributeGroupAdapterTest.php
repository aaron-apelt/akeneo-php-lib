<?php

declare(strict_types=1);

use Akeneo\Pim\ApiClient\Api\AttributeGroupApiInterface;
use AkeneoLib\Adapter\AttributeGroupAdapter;
use AkeneoLib\Entity\AttributeGroup;
use AkeneoLib\Search\QueryParameter;
use AkeneoLib\Serializer\SerializerInterface;

beforeEach(function () {
    $this->attributeGroupApi = mock(AttributeGroupApiInterface::class);
    $this->serializer = mock(SerializerInterface::class);
    $this->adapter = new AttributeGroupAdapter($this->attributeGroupApi, $this->serializer);
});

it('yields denormalized attribute groups from all()', function () {
    $apiGroup = ['code' => 'group-1'];
    $groupObj = new AttributeGroup('group-1');

    $cursor = resourceCursorMock([$apiGroup]);
    $this->attributeGroupApi->shouldReceive('all')->andReturn($cursor);
    $this->serializer->shouldReceive('denormalize')->with($apiGroup, AttributeGroup::class)->andReturn($groupObj);

    $results = iterator_to_array($this->adapter->all());
    expect($results)->toHaveCount(1)->and($results[0])->toBe($groupObj);
});

it('uses query parameters in all()', function () {
    $apiGroup = ['code' => 'group-2'];
    $groupObj = new AttributeGroup('group-2');
    $param = mock(QueryParameter::class);
    $param->shouldReceive('toArray')->andReturn(['limit' => 20]);
    $cursor = resourceCursorMock([$apiGroup]);
    $this->attributeGroupApi->shouldReceive('all')->with(100, ['limit' => 20])->andReturn($cursor);
    $this->serializer->shouldReceive('denormalize')->with($apiGroup, AttributeGroup::class)->andReturn($groupObj);

    $results = iterator_to_array($this->adapter->all($param));
    expect($results)->toHaveCount(1)->and($results[0])->toBe($groupObj);
});

it('gets and denormalizes an attribute group by code', function () {
    $apiGroup = ['code' => 'group-3'];
    $groupObj = new AttributeGroup('group-3');
    $this->attributeGroupApi->shouldReceive('get')->with('group-3')->andReturn($apiGroup);
    $this->serializer->shouldReceive('denormalize')->with($apiGroup, AttributeGroup::class)->andReturn($groupObj);

    expect($this->adapter->get('group-3'))->toBe($groupObj);
});
