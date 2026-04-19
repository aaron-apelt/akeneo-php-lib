<?php

declare(strict_types=1);

use Akeneo\Pim\ApiClient\Api\ReferenceEntityRecordApiInterface;
use AkeneoLib\Adapter\ReferenceEntityRecordAdapter;
use AkeneoLib\Entity\ReferenceEntityRecord;
use AkeneoLib\Search\QueryParameter;
use AkeneoLib\Serializer\SerializerInterface;

beforeEach(function () {
    $this->referenceEntityRecordApi = mock(ReferenceEntityRecordApiInterface::class);
    $this->serializer = mock(SerializerInterface::class);
    $this->adapter = new ReferenceEntityRecordAdapter($this->referenceEntityRecordApi, $this->serializer);
});

it('gets and sets reference entity code', function () {
    expect($this->adapter->getReferenceEntityCode())->toBe('');
    $this->adapter->setReferenceEntityCode('brand');
    expect($this->adapter->getReferenceEntityCode())->toBe('brand');
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

    expect($this->adapter->get('adidas'))->toBe($recordObj);
});
