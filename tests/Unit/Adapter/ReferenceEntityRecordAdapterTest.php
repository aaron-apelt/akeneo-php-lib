<?php

declare(strict_types=1);

use Akeneo\Pim\ApiClient\Api\ReferenceEntityRecordApiInterface;
use AkeneoLib\Adapter\ReferenceEntityRecordAdapter;
use AkeneoLib\Adapter\ReferenceEntityRecordAdapterInterface;
use AkeneoLib\Entity\ReferenceEntityRecord;
use AkeneoLib\Search\QueryParameter;
use AkeneoLib\Serializer\SerializerInterface;

beforeEach(function () {
    $this->referenceEntityRecordApi = mock(ReferenceEntityRecordApiInterface::class);
    $this->serializer = mock(SerializerInterface::class);
    $this->adapter = new ReferenceEntityRecordAdapter($this->referenceEntityRecordApi, $this->serializer);
});

it('implements ReferenceEntityRecordAdapterInterface', function () {
    expect($this->adapter)->toBeInstanceOf(ReferenceEntityRecordAdapterInterface::class);
});

it('gets and sets batch size', function () {
    expect($this->adapter->getBatchSize())->toBe(100);
    $this->adapter->setBatchSize(75);
    expect($this->adapter->getBatchSize())->toBe(75);
});

it('gets and sets reference entity code', function () {
    expect($this->adapter->getReferenceEntityCode())->toBe('');
    $this->adapter->setReferenceEntityCode('brand');
    expect($this->adapter->getReferenceEntityCode())->toBe('brand');
});

it('registers a response callback', function () {
    $called = false;
    $cb = function () use (&$called) {
        $called = true;
    };
    $this->adapter->onResponse($cb);
    $this->adapter->setReferenceEntityCode('brand');

    $record = new ReferenceEntityRecord('nike');
    $this->serializer->shouldReceive('normalize')->andReturn([[]]);
    $this->referenceEntityRecordApi->shouldReceive('upsertList')->andReturn([]);
    $this->adapter->stage($record);
    $this->adapter->push();
    expect($called)->toBeTrue();
});

it('yields denormalized reference entity records from all()', function () {
    $this->adapter->setReferenceEntityCode('brand');
    $apiRecord = ['code' => 'nike'];
    $recordObj = new ReferenceEntityRecord('nike');

    $cursor = resourceCursorMock([$apiRecord]);
    $this->referenceEntityRecordApi->shouldReceive('all')->andReturn($cursor);
    $this->serializer->shouldReceive('denormalize')->with($apiRecord, ReferenceEntityRecord::class, ['scopeName' => 'channel'])->andReturn($recordObj);

    $results = iterator_to_array($this->adapter->all());
    expect($results)->toHaveCount(1)->and($results[0])->toBe($recordObj);
});

it('uses query parameters in all()', function () {
    $this->adapter->setReferenceEntityCode('designer');
    $apiRecord = ['code' => 'gucci'];
    $recordObj = new ReferenceEntityRecord('gucci');
    $param = mock(QueryParameter::class);
    $param->shouldReceive('toArray')->andReturn(['limit' => 10]);
    $cursor = resourceCursorMock([$apiRecord]);
    $this->referenceEntityRecordApi->shouldReceive('all')->with('designer', ['limit' => 10])->andReturn($cursor);
    $this->serializer->shouldReceive('denormalize')->with($apiRecord, ReferenceEntityRecord::class, ['scopeName' => 'channel'])->andReturn($recordObj);

    $results = iterator_to_array($this->adapter->all($param));
    expect($results)->toHaveCount(1)->and($results[0])->toBe($recordObj);
});

it('gets and denormalizes a reference entity record by code', function () {
    $this->adapter->setReferenceEntityCode('brand');
    $apiRecord = ['code' => 'adidas'];
    $recordObj = new ReferenceEntityRecord('adidas');
    $this->referenceEntityRecordApi->shouldReceive('get')->with('brand', 'adidas')->andReturn($apiRecord);
    $this->serializer->shouldReceive('denormalize')->with($apiRecord, ReferenceEntityRecord::class, ['scopeName' => 'channel'])->andReturn($recordObj);

    $result = $this->adapter->get('adidas');
    expect($result)->toBe($recordObj);
});

it('stages reference entity records and pushes when batch size is met', function () {
    $this->adapter->setBatchSize(2);
    $this->adapter->setReferenceEntityCode('brand');
    $r1 = new ReferenceEntityRecord('puma');
    $r2 = new ReferenceEntityRecord('reebok');
    $this->serializer->shouldReceive('normalize')->once()->andReturn([[], []]);
    $this->referenceEntityRecordApi->shouldReceive('upsertList')->once()->andReturn([]);

    $this->adapter->stage($r1);
    $this->adapter->stage($r2);
    $this->adapter->push();
    expect(true)->toBeTrue();
});

it('pushes staged reference entity records and clears the queue', function () {
    $this->adapter->setReferenceEntityCode('brand');
    $record = new ReferenceEntityRecord('underarmour');
    $this->serializer->shouldReceive('normalize')->once()->andReturn([[]]);
    $this->referenceEntityRecordApi->shouldReceive('upsertList')->once()->andReturn([]);
    $this->adapter->stage($record);
    $this->adapter->push();
    $this->adapter->push();
    expect(true)->toBeTrue();
});
