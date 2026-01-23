<?php

declare(strict_types=1);

use Akeneo\Pim\ApiClient\Api\ReferenceEntityApiInterface;
use AkeneoLib\Adapter\ReferenceEntityAdapter;
use AkeneoLib\Adapter\ReferenceEntityAdapterInterface;
use AkeneoLib\Entity\ReferenceEntity;
use AkeneoLib\Search\QueryParameter;
use AkeneoLib\Serializer\SerializerInterface;

beforeEach(function () {
    $this->referenceEntityApi = mock(ReferenceEntityApiInterface::class);
    $this->serializer = mock(SerializerInterface::class);
    $this->adapter = new ReferenceEntityAdapter($this->referenceEntityApi, $this->serializer);
});

it('implements ReferenceEntityAdapterInterface', function () {
    expect($this->adapter)->toBeInstanceOf(ReferenceEntityAdapterInterface::class);
});

it('yields denormalized reference entities from all()', function () {
    $apiEntity = ['code' => 'brand'];
    $entityObj = new ReferenceEntity('brand');

    $cursor = resourceCursorMock([$apiEntity]);
    $this->referenceEntityApi->shouldReceive('all')->andReturn($cursor);
    $this->serializer->shouldReceive('denormalize')->with($apiEntity, ReferenceEntity::class, ['scopeName' => 'channel'])->andReturn($entityObj);

    $results = iterator_to_array($this->adapter->all());
    expect($results)->toHaveCount(1)->and($results[0])->toBe($entityObj);
});

it('uses query parameters in all()', function () {
    $apiEntity = ['code' => 'designer'];
    $entityObj = new ReferenceEntity('designer');
    $param = mock(QueryParameter::class);
    $param->shouldReceive('toArray')->andReturn(['limit' => 10]);
    $cursor = resourceCursorMock([$apiEntity]);
    $this->referenceEntityApi->shouldReceive('all')->with(['limit' => 10])->andReturn($cursor);
    $this->serializer->shouldReceive('denormalize')->with($apiEntity, ReferenceEntity::class, ['scopeName' => 'channel'])->andReturn($entityObj);

    $results = iterator_to_array($this->adapter->all($param));
    expect($results)->toHaveCount(1)->and($results[0])->toBe($entityObj);
});

it('gets and denormalizes a reference entity by code', function () {
    $apiEntity = ['code' => 'manufacturer'];
    $entityObj = new ReferenceEntity('manufacturer');
    $this->referenceEntityApi->shouldReceive('get')->with('manufacturer')->andReturn($apiEntity);
    $this->serializer->shouldReceive('denormalize')->with($apiEntity, ReferenceEntity::class, ['scopeName' => 'channel'])->andReturn($entityObj);

    $result = $this->adapter->get('manufacturer');
    expect($result)->toBe($entityObj);
});

it('upserts a reference entity', function () {
    $entity = new ReferenceEntity('supplier');
    $normalized = ['code' => 'supplier', 'labels' => []];
    $this->serializer->shouldReceive('normalize')->with($entity, ['scopeName' => 'channel'])->andReturn($normalized);
    $this->referenceEntityApi->shouldReceive('upsert')->with('supplier', $normalized)->once();

    $this->adapter->upsert($entity);
    expect(true)->toBeTrue();
});
