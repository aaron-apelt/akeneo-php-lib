<?php

declare(strict_types=1);

use Akeneo\Pim\ApiClient\Api\CurrencyApiInterface;
use AkeneoLib\Adapter\CurrencyAdapter;
use AkeneoLib\Adapter\CurrencyAdapterInterface;
use AkeneoLib\Entity\Currency;
use AkeneoLib\Search\QueryParameter;
use AkeneoLib\Serializer\SerializerInterface;

beforeEach(function () {
    $this->currencyApi = mock(CurrencyApiInterface::class);
    $this->serializer = mock(SerializerInterface::class);
    $this->adapter = new CurrencyAdapter($this->currencyApi, $this->serializer);
});

it('implements CurrencyAdapterInterface', function () {
    expect($this->adapter)->toBeInstanceOf(CurrencyAdapterInterface::class);
});

it('yields denormalized currencies from all()', function () {
    $apiCurrency = ['code' => 'USD'];
    $currencyObj = new Currency('USD');

    $cursor = resourceCursorMock([$apiCurrency]);
    $this->currencyApi->shouldReceive('all')->andReturn($cursor);
    $this->serializer->shouldReceive('denormalize')->with($apiCurrency, Currency::class)->andReturn($currencyObj);

    $results = iterator_to_array($this->adapter->all());
    expect($results)->toHaveCount(1)->and($results[0])->toBe($currencyObj);
});

it('uses query parameters in all()', function () {
    $apiCurrency = ['code' => 'EUR'];
    $currencyObj = new Currency('EUR');
    $param = mock(QueryParameter::class);
    $param->shouldReceive('toArray')->andReturn(['enabled' => true]);
    $cursor = resourceCursorMock([$apiCurrency]);
    $this->currencyApi->shouldReceive('all')->with(100, ['enabled' => true])->andReturn($cursor);
    $this->serializer->shouldReceive('denormalize')->with($apiCurrency, Currency::class)->andReturn($currencyObj);

    $results = iterator_to_array($this->adapter->all($param));
    expect($results)->toHaveCount(1)->and($results[0])->toBe($currencyObj);
});

it('gets and denormalizes a currency by code', function () {
    $apiCurrency = ['code' => 'GBP'];
    $currencyObj = new Currency('GBP');
    $this->currencyApi->shouldReceive('get')->with('GBP')->andReturn($apiCurrency);
    $this->serializer->shouldReceive('denormalize')->with($apiCurrency, Currency::class)->andReturn($currencyObj);

    $result = $this->adapter->get('GBP');
    expect($result)->toBe($currencyObj);
});
