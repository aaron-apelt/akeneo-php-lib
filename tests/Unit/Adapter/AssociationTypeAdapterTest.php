<?php

declare(strict_types=1);

use Akeneo\Pim\ApiClient\Api\AssociationTypeApiInterface;
use AkeneoLib\Adapter\AssociationTypeAdapter;
use AkeneoLib\Entity\AssociationType;
use AkeneoLib\Search\QueryParameter;
use AkeneoLib\Serializer\SerializerInterface;

beforeEach(function () {
    $this->associationTypeApi = mock(AssociationTypeApiInterface::class);
    $this->serializer = mock(SerializerInterface::class);
    $this->adapter = new AssociationTypeAdapter($this->associationTypeApi, $this->serializer);
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

    expect($this->adapter->get('assoc-3'))->toBe($associationTypeObj);
});
