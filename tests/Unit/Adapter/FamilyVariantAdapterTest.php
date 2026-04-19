<?php

declare(strict_types=1);

use Akeneo\Pim\ApiClient\Api\FamilyVariantApiInterface;
use AkeneoLib\Adapter\FamilyVariantAdapter;
use AkeneoLib\Entity\FamilyVariant;
use AkeneoLib\Search\QueryParameter;
use AkeneoLib\Serializer\SerializerInterface;

beforeEach(function () {
    $this->familyVariantApi = mock(FamilyVariantApiInterface::class);
    $this->serializer = mock(SerializerInterface::class);
    $this->adapter = new FamilyVariantAdapter($this->familyVariantApi, $this->serializer);
    $this->adapter->setFamilyCode('family');
});

it('gets and sets family code', function () {
    expect($this->adapter->getFamilyCode())->toBe('family');
    $this->adapter->setFamilyCode('clothing');
    expect($this->adapter->getFamilyCode())->toBe('clothing');
});

it('yields denormalized family variants from all()', function () {
    $apiVariant = ['code' => 'variant-1'];
    $variantObj = new FamilyVariant('variant-1');

    $cursor = resourceCursorMock([$apiVariant]);
    $this->familyVariantApi->shouldReceive('all')->andReturn($cursor);
    $this->serializer->shouldReceive('denormalize')->with($apiVariant, FamilyVariant::class)->andReturn($variantObj);

    $results = iterator_to_array($this->adapter->all());
    expect($results)->toHaveCount(1)->and($results[0])->toBe($variantObj);
});

it('uses query parameters in all()', function () {
    $apiVariant = ['code' => 'variant-2'];
    $variantObj = new FamilyVariant('variant-2');
    $param = mock(QueryParameter::class);
    $param->shouldReceive('toArray')->andReturn([]);
    $cursor = resourceCursorMock([$apiVariant]);
    $this->familyVariantApi->shouldReceive('all')->with('family', 100, [])->andReturn($cursor);
    $this->serializer->shouldReceive('denormalize')->with($apiVariant, FamilyVariant::class)->andReturn($variantObj);

    $results = iterator_to_array($this->adapter->all($param));
    expect($results)->toHaveCount(1)->and($results[0])->toBe($variantObj);
});

it('gets and denormalizes a family variant by code', function () {
    $apiVariant = ['code' => 'variant-3'];
    $variantObj = new FamilyVariant('variant-3');
    $this->familyVariantApi->shouldReceive('get')->with('family', 'variant-3')->andReturn($apiVariant);
    $this->serializer->shouldReceive('denormalize')->with($apiVariant, FamilyVariant::class)->andReturn($variantObj);

    expect($this->adapter->get('variant-3'))->toBe($variantObj);
});
