<?php

declare(strict_types=1);

use Akeneo\Pim\ApiClient\Api\AttributeGroupApiInterface;
use AkeneoLib\Adapter\AttributeGroupAdapter;
use AkeneoLib\Adapter\AttributeGroupAdapterInterface;
use AkeneoLib\Entity\AttributeGroup;
use AkeneoLib\Search\QueryParameter;
use AkeneoLib\Serializer\SerializerInterface;

beforeEach(function () {
    $this->attributeGroupApi = mock(AttributeGroupApiInterface::class);
    $this->serializer = mock(SerializerInterface::class);
    $this->adapter = new AttributeGroupAdapter($this->attributeGroupApi, $this->serializer);
});

it('implements AttributeGroupAdapterInterface', function () {
    expect($this->adapter)->toBeInstanceOf(AttributeGroupAdapterInterface::class);
});

it('gets and sets batch size', function () {
    expect($this->adapter->getBatchSize())->toBe(100);
    $this->adapter->setBatchSize(35);
    expect($this->adapter->getBatchSize())->toBe(35);
});

it('registers a response callback', function () {
    $called = false;
    $cb = function () use (&$called) {
        $called = true;
    };
    $this->adapter->onResponse($cb);

    $group = new AttributeGroup('group-1');
    $this->serializer->shouldReceive('normalize')->andReturn([[]]);
    $this->attributeGroupApi->shouldReceive('upsertList')->andReturn(new ArrayIterator([]));
    $this->adapter->stage($group);
    $this->adapter->push();
    expect($called)->toBeTrue();
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
    $param->shouldReceive('toArray')->andReturn(['search' => 'test']);
    $cursor = resourceCursorMock([$apiGroup]);
    $this->attributeGroupApi->shouldReceive('all')->with(100, ['search' => 'test'])->andReturn($cursor);
    $this->serializer->shouldReceive('denormalize')->with($apiGroup, AttributeGroup::class)->andReturn($groupObj);

    $results = iterator_to_array($this->adapter->all($param));
    expect($results)->toHaveCount(1)->and($results[0])->toBe($groupObj);
});

it('gets and denormalizes an attribute group by code', function () {
    $apiGroup = ['code' => 'group-3'];
    $groupObj = new AttributeGroup('group-3');
    $this->attributeGroupApi->shouldReceive('get')->with('group-3')->andReturn($apiGroup);
    $this->serializer->shouldReceive('denormalize')->with($apiGroup, AttributeGroup::class)->andReturn($groupObj);

    $result = $this->adapter->get('group-3');
    expect($result)->toBe($groupObj);
});

it('stages attribute groups and pushes when batch size is met', function () {
    $this->adapter->setBatchSize(2);
    $g1 = new AttributeGroup('group-a');
    $g2 = new AttributeGroup('group-b');
    $this->serializer->shouldReceive('normalize')->once()->andReturn([[], []]);
    $this->attributeGroupApi->shouldReceive('upsertList')->once()->andReturn(new ArrayIterator([]));

    $this->adapter->stage($g1);
    $this->adapter->stage($g2);
    $this->adapter->push();
    expect(true)->toBeTrue();
});

it('pushes staged attribute groups and clears the queue', function () {
    $group = new AttributeGroup('group-x');
    $this->serializer->shouldReceive('normalize')->once()->andReturn([[]]);
    $this->attributeGroupApi->shouldReceive('upsertList')->once()->andReturn(new ArrayIterator([]));
    $this->adapter->stage($group);
    $this->adapter->push();
    $this->adapter->push();
    expect(true)->toBeTrue();
});
