<?php

declare(strict_types=1);

use Akeneo\Pim\ApiClient\Api\FamilyApiInterface;
use AkeneoLib\Adapter\FamilyAdapter;
use AkeneoLib\Adapter\FamilyAdapterInterface;
use AkeneoLib\Entity\Family;
use AkeneoLib\Search\QueryParameter;
use AkeneoLib\Serializer\SerializerInterface;

beforeEach(function () {
    $this->familyApi = mock(FamilyApiInterface::class);
    $this->serializer = mock(SerializerInterface::class);
    $this->adapter = new FamilyAdapter($this->familyApi, $this->serializer);
});

it('implements FamilyAdapterInterface', function () {
    expect($this->adapter)->toBeInstanceOf(FamilyAdapterInterface::class);
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

    $family = new Family('fam-1');
    $this->serializer->shouldReceive('normalize')->andReturn([[]]);
    $this->familyApi->shouldReceive('upsertList')->andReturn(new ArrayIterator([]));
    $this->adapter->stage($family);
    $this->adapter->push();
    expect($called)->toBeTrue();
});

it('yields denormalized families from all()', function () {
    $apiFamily = ['code' => 'fam-1'];
    $familyObj = new Family('fam-1');

    $cursor = resourceCursorMock([$apiFamily]);
    $this->familyApi->shouldReceive('all')->andReturn($cursor);
    $this->serializer->shouldReceive('denormalize')->with($apiFamily, Family::class)->andReturn($familyObj);

    $results = iterator_to_array($this->adapter->all());
    expect($results)->toHaveCount(1)->and($results[0])->toBe($familyObj);
});

it('uses query parameters in all()', function () {
    $apiFamily = ['code' => 'fam-2'];
    $familyObj = new Family('fam-2');
    $param = mock(QueryParameter::class);
    $param->shouldReceive('toArray')->andReturn(['foo' => 'bar']);
    $cursor = resourceCursorMock([$apiFamily]);
    $this->familyApi->shouldReceive('all')->with(100, ['foo' => 'bar'])->andReturn($cursor);
    $this->serializer->shouldReceive('denormalize')->with($apiFamily, Family::class)->andReturn($familyObj);

    $results = iterator_to_array($this->adapter->all($param));
    expect($results)->toHaveCount(1)->and($results[0])->toBe($familyObj);
});

it('gets and denormalizes a family by code', function () {
    $apiFamily = ['code' => 'fam-3'];
    $familyObj = new Family('fam-3');
    $this->familyApi->shouldReceive('get')->with('fam-3')->andReturn($apiFamily);
    $this->serializer->shouldReceive('denormalize')->with($apiFamily, Family::class)->andReturn($familyObj);

    $result = $this->adapter->get('fam-3');
    expect($result)->toBe($familyObj);
});

it('stages families and pushes when batch size is met', function () {
    $this->adapter->setBatchSize(2);
    $f1 = new Family('fam-a');
    $f2 = new Family('fam-b');
    $this->serializer->shouldReceive('normalize')->once()->andReturn([[], []]);
    $this->familyApi->shouldReceive('upsertList')->once()->andReturn(new ArrayIterator([]));

    $this->adapter->stage($f1);
    $this->adapter->stage($f2);
    $this->adapter->push();
    expect(true)->toBeTrue();
});

it('pushes staged families and clears the queue', function () {
    $family = new Family('fam-x');
    $this->serializer->shouldReceive('normalize')->once()->andReturn([[]]);
    $this->familyApi->shouldReceive('upsertList')->once()->andReturn(new ArrayIterator([]));
    $this->adapter->stage($family);
    $this->adapter->push();
    $this->adapter->push();
    expect(true)->toBeTrue();
});
