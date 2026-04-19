<?php

declare(strict_types=1);

use Akeneo\Pim\ApiClient\Api\FamilyApiInterface;
use AkeneoLib\Adapter\FamilyAdapter;
use AkeneoLib\Entity\Family;
use AkeneoLib\Search\QueryParameter;
use AkeneoLib\Serializer\SerializerInterface;

beforeEach(function () {
    $this->familyApi = mock(FamilyApiInterface::class);
    $this->serializer = mock(SerializerInterface::class);
    $this->adapter = new FamilyAdapter($this->familyApi, $this->serializer);
});

it('yields denormalized families from all()', function () {
    $apiFamily = ['code' => 'clothing'];
    $familyObj = new Family('clothing');

    $cursor = resourceCursorMock([$apiFamily]);
    $this->familyApi->shouldReceive('all')->andReturn($cursor);
    $this->serializer->shouldReceive('denormalize')->with($apiFamily, Family::class)->andReturn($familyObj);

    $results = iterator_to_array($this->adapter->all());
    expect($results)->toHaveCount(1)->and($results[0])->toBe($familyObj);
});

it('uses query parameters in all()', function () {
    $apiFamily = ['code' => 'shoes'];
    $familyObj = new Family('shoes');
    $param = mock(QueryParameter::class);
    $param->shouldReceive('toArray')->andReturn(['limit' => 10]);
    $cursor = resourceCursorMock([$apiFamily]);
    $this->familyApi->shouldReceive('all')->with(100, ['limit' => 10])->andReturn($cursor);
    $this->serializer->shouldReceive('denormalize')->with($apiFamily, Family::class)->andReturn($familyObj);

    $results = iterator_to_array($this->adapter->all($param));
    expect($results)->toHaveCount(1)->and($results[0])->toBe($familyObj);
});

it('gets and denormalizes a family by code', function () {
    $apiFamily = ['code' => 'accessories'];
    $familyObj = new Family('accessories');
    $this->familyApi->shouldReceive('get')->with('accessories')->andReturn($apiFamily);
    $this->serializer->shouldReceive('denormalize')->with($apiFamily, Family::class)->andReturn($familyObj);

    expect($this->adapter->get('accessories'))->toBe($familyObj);
});
