<?php

declare(strict_types=1);

use Akeneo\Pim\ApiClient\Api\AssociationTypeApiInterface;
use AkeneoLib\Adapter\AssociationTypeAdapter;
use AkeneoLib\Adapter\AssociationTypeAdapterInterface;
use AkeneoLib\Entity\AssociationType;
use AkeneoLib\Search\QueryParameter;
use AkeneoLib\Serializer\SerializerInterface;

beforeEach(function () {
    $this->associationTypeApi = mock(AssociationTypeApiInterface::class);
    $this->serializer = mock(SerializerInterface::class);
    $this->adapter = new AssociationTypeAdapter($this->associationTypeApi, $this->serializer);
});

it('implements AssociationTypeAdapterInterface', function () {
    expect($this->adapter)->toBeInstanceOf(AssociationTypeAdapterInterface::class);
});

it('gets and sets batch size', function () {
    expect($this->adapter->getBatchSize())->toBe(100);
    $this->adapter->setBatchSize(40);
    expect($this->adapter->getBatchSize())->toBe(40);
});

it('registers a response callback', function () {
    $called = false;
    $cb = function () use (&$called) {
        $called = true;
    };
    $this->adapter->onResponse($cb);

    $associationType = new AssociationType('assoc-1');
    $this->serializer->shouldReceive('normalize')->andReturn([[]]);
    $this->associationTypeApi->shouldReceive('upsertList')->andReturn(new ArrayIterator([]));
    $this->adapter->stage($associationType);
    $this->adapter->push();
    expect($called)->toBeTrue();
});

it('yields denormalized association types from all()', function () {
    $apiAssociationType = ['code' => 'assoc-1'];
    $associationTypeObj = new AssociationType('assoc-1');

    $cursor = resourceCursorMock([$apiAssociationType]);
    $this->associationTypeApi->shouldReceive('all')->andReturn($cursor);
    $this->serializer->shouldReceive('denormalize')->with($apiAssociationType, AssociationType::class)->andReturn($associationTypeObj);

    $results = iterator_to_array($this->adapter->all());
    expect($results)->toHaveCount(1)->and($results[0])->toBe($associationTypeObj);
});

it('uses query parameters in all()', function () {
    $apiAssociationType = ['code' => 'assoc-2'];
    $associationTypeObj = new AssociationType('assoc-2');
    $param = mock(QueryParameter::class);
    $param->shouldReceive('toArray')->andReturn(['limit' => 50]);
    $cursor = resourceCursorMock([$apiAssociationType]);
    $this->associationTypeApi->shouldReceive('all')->with(100, ['limit' => 50])->andReturn($cursor);
    $this->serializer->shouldReceive('denormalize')->with($apiAssociationType, AssociationType::class)->andReturn($associationTypeObj);

    $results = iterator_to_array($this->adapter->all($param));
    expect($results)->toHaveCount(1)->and($results[0])->toBe($associationTypeObj);
});

it('gets and denormalizes an association type by code', function () {
    $apiAssociationType = ['code' => 'assoc-3'];
    $associationTypeObj = new AssociationType('assoc-3');
    $this->associationTypeApi->shouldReceive('get')->with('assoc-3')->andReturn($apiAssociationType);
    $this->serializer->shouldReceive('denormalize')->with($apiAssociationType, AssociationType::class)->andReturn($associationTypeObj);

    $result = $this->adapter->get('assoc-3');
    expect($result)->toBe($associationTypeObj);
});

it('stages association types and pushes when batch size is met', function () {
    $this->adapter->setBatchSize(2);
    $a1 = new AssociationType('assoc-a');
    $a2 = new AssociationType('assoc-b');
    $this->serializer->shouldReceive('normalize')->once()->andReturn([[], []]);
    $this->associationTypeApi->shouldReceive('upsertList')->once()->andReturn(new ArrayIterator([]));

    $this->adapter->stage($a1);
    $this->adapter->stage($a2);
    $this->adapter->push();
    expect(true)->toBeTrue();
});

it('pushes staged association types and clears the queue', function () {
    $associationType = new AssociationType('assoc-x');
    $this->serializer->shouldReceive('normalize')->once()->andReturn([[]]);
    $this->associationTypeApi->shouldReceive('upsertList')->once()->andReturn(new ArrayIterator([]));
    $this->adapter->stage($associationType);
    $this->adapter->push();
    $this->adapter->push();
    expect(true)->toBeTrue();
});
