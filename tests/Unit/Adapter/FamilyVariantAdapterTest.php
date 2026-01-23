<?php

declare(strict_types=1);

use Akeneo\Pim\ApiClient\Api\FamilyVariantApiInterface;
use AkeneoLib\Adapter\FamilyVariantAdapter;
use AkeneoLib\Adapter\FamilyVariantAdapterInterface;
use AkeneoLib\Entity\FamilyVariant;
use AkeneoLib\Search\QueryParameter;
use AkeneoLib\Serializer\SerializerInterface;

beforeEach(function () {
    $this->familyVariantApi = mock(FamilyVariantApiInterface::class);
    $this->serializer = mock(SerializerInterface::class);
    $this->adapter = new FamilyVariantAdapter($this->familyVariantApi, $this->serializer);
});

it('implements FamilyVariantAdapterInterface', function () {
    expect($this->adapter)->toBeInstanceOf(FamilyVariantAdapterInterface::class);
});

it('gets and sets batch size', function () {
    expect($this->adapter->getBatchSize())->toBe(100);
    $this->adapter->setBatchSize(60);
    expect($this->adapter->getBatchSize())->toBe(60);
});

it('registers a response callback', function () {
    $called = false;
    $cb = function () use (&$called) {
        $called = true;
    };
    $this->adapter->onResponse($cb);

    $variant = new FamilyVariant('fam', 'variant-1');
    $this->serializer->shouldReceive('normalize')->andReturn([[]]);
    $this->familyVariantApi->shouldReceive('upsertList')->andReturn(new ArrayIterator([]));
    $this->adapter->stage($variant);
    $this->adapter->push('fam');
    expect($called)->toBeTrue();
});

it('yields denormalized family variants from all()', function () {
    $apiVariant = ['code' => 'variant-1'];
    $variantObj = new FamilyVariant('family_test', 'variant-1');

    $cursor = resourceCursorMock([$apiVariant]);
    $this->familyVariantApi->shouldReceive('all')->andReturn($cursor);
    $this->serializer->shouldReceive('denormalize')->with($apiVariant, FamilyVariant::class)->andReturn($variantObj);

    $results = iterator_to_array($this->adapter->all('family_test'));
    expect($results)->toHaveCount(1)->and($results[0])->toBe($variantObj);
});

it('uses query parameters in all()', function () {
    $apiVariant = ['code' => 'variant-2'];
    $variantObj = new FamilyVariant('family_test', 'variant-2');
    $param = mock(QueryParameter::class);
    $param->shouldReceive('toArray')->andReturn(['limit' => 10]);
    $cursor = resourceCursorMock([$apiVariant]);
    $this->familyVariantApi->shouldReceive('all')->with('family_test', 100, ['limit' => 10])->andReturn($cursor);
    $this->serializer->shouldReceive('denormalize')->with($apiVariant, FamilyVariant::class)->andReturn($variantObj);

    $results = iterator_to_array($this->adapter->all('family_test', $param));
    expect($results)->toHaveCount(1)->and($results[0])->toBe($variantObj);
});

it('gets and denormalizes a family variant by code', function () {
    $apiVariant = ['code' => 'variant-3'];
    $variantObj = new FamilyVariant('family_test', 'variant-3');
    $this->familyVariantApi->shouldReceive('get')->with('family_test', 'variant-3')->andReturn($apiVariant);
    $this->serializer->shouldReceive('denormalize')->with($apiVariant, FamilyVariant::class)->andReturn($variantObj);

    $result = $this->adapter->get('family_test', 'variant-3');
    expect($result)->toBe($variantObj);
});

it('stages family variants and pushes when batch size is met', function () {
    $this->adapter->setBatchSize(2);
    $v1 = new FamilyVariant('fam', 'variant-a');
    $v2 = new FamilyVariant('fam', 'variant-b');
    $this->serializer->shouldReceive('normalize')->once()->andReturn([[], []]);
    $this->familyVariantApi->shouldReceive('upsertList')->once()->andReturn(new ArrayIterator([]));

    $this->adapter->stage($v1);
    $this->adapter->stage($v2);
    $this->adapter->push('fam');
    expect(true)->toBeTrue();
});

it('pushes staged family variants and clears the queue', function () {
    $variant = new FamilyVariant('fam', 'variant-x');
    $this->serializer->shouldReceive('normalize')->once()->andReturn([[]]);
    $this->familyVariantApi->shouldReceive('upsertList')->once()->andReturn(new ArrayIterator([]));
    $this->adapter->stage($variant);
    $this->adapter->push('fam');
    $this->adapter->push('fam');
    expect(true)->toBeTrue();
});
