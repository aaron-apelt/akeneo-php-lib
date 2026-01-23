<?php

declare(strict_types=1);

use AkeneoLib\Entity\Locale;

describe('code management', function () {
    it('can get the code', function () {
        $locale = new Locale('en_US');
        expect($locale->getCode())->toBe('en_US');
    });

    it('can set the code', function () {
        $locale = new Locale('en_US')->setCode('fr_FR');
        expect($locale->getCode())->toBe('fr_FR');
    });
});

describe('enabled status management', function () {
    it('can check if enabled (initially null)', function () {
        $locale = new Locale('en_US');
        expect($locale->isEnabled())->toBeNull();
    });

    it('can set enabled to true', function () {
        $locale = new Locale('en_US')->setEnabled(true);
        expect($locale->isEnabled())->toBeTrue();
    });

    it('can set enabled to false', function () {
        $locale = new Locale('de_DE')->setEnabled(false);
        expect($locale->isEnabled())->toBeFalse();
    });
});
