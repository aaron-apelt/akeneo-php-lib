<?php

declare(strict_types=1);

use AkeneoLib\Entity\Currency;

describe('code management', function () {
    it('can get the code', function () {
        $currency = new Currency('USD');
        expect($currency->getCode())->toBe('USD');
    });

    it('can set the code', function () {
        $currency = (new Currency('USD'))->setCode('EUR');
        expect($currency->getCode())->toBe('EUR');
    });
});

describe('enabled status management', function () {
    it('can check if enabled (initially null)', function () {
        $currency = new Currency('USD');
        expect($currency->isEnabled())->toBeNull();
    });

    it('can set enabled to true', function () {
        $currency = (new Currency('USD'))->setEnabled(true);
        expect($currency->isEnabled())->toBeTrue();
    });

    it('can set enabled to false', function () {
        $currency = (new Currency('JPY'))->setEnabled(false);
        expect($currency->isEnabled())->toBeFalse();
    });
});
