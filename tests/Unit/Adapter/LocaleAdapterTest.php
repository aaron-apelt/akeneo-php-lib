<?php

declare(strict_types=1);

use Akeneo\Pim\ApiClient\Api\LocaleApiInterface;
use AkeneoLib\Adapter\LocaleAdapter;
use AkeneoLib\Adapter\LocaleAdapterInterface;
use AkeneoLib\Entity\Locale;
use AkeneoLib\Search\QueryParameter;
use AkeneoLib\Serializer\SerializerInterface;

beforeEach(function () {
    $this->localeApi = mock(LocaleApiInterface::class);
    $this->serializer = mock(SerializerInterface::class);
    $this->adapter = new LocaleAdapter($this->localeApi, $this->serializer);
});

it('implements LocaleAdapterInterface', function () {
    expect($this->adapter)->toBeInstanceOf(LocaleAdapterInterface::class);
});

it('yields denormalized locales from all()', function () {
    $apiLocale = ['code' => 'en_US'];
    $localeObj = new Locale('en_US');

    $cursor = resourceCursorMock([$apiLocale]);
    $this->localeApi->shouldReceive('all')->andReturn($cursor);
    $this->serializer->shouldReceive('denormalize')->with($apiLocale, Locale::class)->andReturn($localeObj);

    $results = iterator_to_array($this->adapter->all());
    expect($results)->toHaveCount(1)->and($results[0])->toBe($localeObj);
});

it('uses query parameters in all()', function () {
    $apiLocale = ['code' => 'fr_FR'];
    $localeObj = new Locale('fr_FR');
    $param = mock(QueryParameter::class);
    $param->shouldReceive('toArray')->andReturn(['enabled' => true]);
    $cursor = resourceCursorMock([$apiLocale]);
    $this->localeApi->shouldReceive('all')->with(100, ['enabled' => true])->andReturn($cursor);
    $this->serializer->shouldReceive('denormalize')->with($apiLocale, Locale::class)->andReturn($localeObj);

    $results = iterator_to_array($this->adapter->all($param));
    expect($results)->toHaveCount(1)->and($results[0])->toBe($localeObj);
});

it('gets and denormalizes a locale by code', function () {
    $apiLocale = ['code' => 'de_DE'];
    $localeObj = new Locale('de_DE');
    $this->localeApi->shouldReceive('get')->with('de_DE')->andReturn($apiLocale);
    $this->serializer->shouldReceive('denormalize')->with($apiLocale, Locale::class)->andReturn($localeObj);

    $result = $this->adapter->get('de_DE');
    expect($result)->toBe($localeObj);
});
