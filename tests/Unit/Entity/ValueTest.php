<?php

declare(strict_types=1);

use AkeneoLib\Entity\Value;

describe('attribute code management', function () {
    it('can get the attribute code', function () {
        $value = new Value('initial_code', null);
        expect($value->getAttributeCode())->toBe('initial_code');
    });
    it('can set the attribute code', function () {
        $value = new Value('old_code', null)->setAttributeCode('new_code');
        expect($value->getAttributeCode())->toBe('new_code');
    });
});

describe('data management', function () {
    it('can get the data (initially null)', function () {
        $value = new Value('code', null);
        expect($value->getData())->toBeNull();
    });
    it('can set string data', function () {
        $value = new Value('code', null)->setData('string_data');
        expect($value->getData())->toBe('string_data');
    });
    it('can set integer data', function () {
        $value = new Value('code', null)->setData(123);
        expect($value->getData())->toBe(123);
    });
    it('can set float data', function () {
        $value = new Value('code', null)->setData(12.34);
        expect($value->getData())->toBe(12.34);
    });
    it('can set boolean data (true)', function () {
        $value = new Value('code', null)->setData(true);
        expect($value->getData())->toBeTrue();
    });
    it('can set boolean data (false)', function () {
        $value = new Value('code', null)->setData(false);
        expect($value->getData())->toBeFalse();
    });
    it('can set array data', function () {
        $value = new Value('code', null)->setData(['a', 'b']);
        expect($value->getData())->toBe(['a', 'b']);
    });
    it('can set null data', function () {
        $value = new Value('code', 'some_data')->setData(null);
        expect($value->getData())->toBeNull();
    });
});

describe('scope management', function () {
    it('can get the scope (initially null)', function () {
        $value = new Value('code', null);
        expect($value->getScope())->toBeNull();
    });
    it('can set the scope', function () {
        $value = new Value('code', null)->setScope('ecommerce');
        expect($value->getScope())->toBe('ecommerce');
    });
    it('can set the scope to null', function () {
        $value = new Value('code', null, 'print')->setScope(null);
        expect($value->getScope())->toBeNull();
    });
});

describe('locale management', function () {
    it('can get the locale (initially null)', function () {
        $value = new Value('code', null);
        expect($value->getLocale())->toBeNull();
    });
    it('can set the locale', function () {
        $value = new Value('code', null)->setLocale('en_US');
        expect($value->getLocale())->toBe('en_US');
    });
    it('can set the locale to null', function () {
        $value = new Value('code', null, null, 'de_DE')->setLocale(null);
        expect($value->getLocale())->toBeNull();
    });
});
