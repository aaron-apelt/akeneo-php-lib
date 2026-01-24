<?php

declare(strict_types=1);

use AkeneoLib\Entity\Channel;

describe('code management', function () {
    it('can get the code', function () {
        $channel = new Channel('ecommerce');
        expect($channel->getCode())->toBe('ecommerce');
    });

    it('can set the code', function () {
        $channel = new Channel('old_code')->setCode('new_code');
        expect($channel->getCode())->toBe('new_code');
    });
});

describe('labels management', function () {
    it('can get the labels (initially null)', function () {
        $channel = new Channel('ecommerce');
        expect($channel->getLabels())->toBeNull();
    });

    it('can set the labels', function () {
        $labels = ['en_US' => 'E-commerce', 'fr_FR' => 'E-commerce'];
        $channel = new Channel('ecommerce')->setLabels($labels);
        expect($channel->getLabels())->toBe($labels);
    });

    it('can set the labels to null', function () {
        $labels = ['en_US' => 'Test'];
        $channel = new Channel('ecommerce')->setLabels($labels)->setLabels(null);
        expect($channel->getLabels())->toBeNull();
    });
});

describe('currencies management', function () {
    it('can get the currencies (initially null)', function () {
        $channel = new Channel('ecommerce');
        expect($channel->getCurrencies())->toBeNull();
    });

    it('can set the currencies', function () {
        $currencies = ['USD', 'EUR'];
        $channel = new Channel('ecommerce')->setCurrencies($currencies);
        expect($channel->getCurrencies())->toBe($currencies);
    });

    it('can set the currencies to null', function () {
        $currencies = ['USD'];
        $channel = new Channel('ecommerce')->setCurrencies($currencies)->setCurrencies(null);
        expect($channel->getCurrencies())->toBeNull();
    });
});

describe('locales management', function () {
    it('can get the locales (initially null)', function () {
        $channel = new Channel('ecommerce');
        expect($channel->getLocales())->toBeNull();
    });

    it('can set the locales', function () {
        $locales = ['en_US', 'fr_FR', 'de_DE'];
        $channel = new Channel('ecommerce')->setLocales($locales);
        expect($channel->getLocales())->toBe($locales);
    });

    it('can set the locales to null', function () {
        $channel = new Channel('ecommerce')->setLocales(['en_US'])->setLocales(null);
        expect($channel->getLocales())->toBeNull();
    });
});

describe('category tree management', function () {
    it('can get the category tree (initially null)', function () {
        $channel = new Channel('ecommerce');
        expect($channel->getCategoryTree())->toBeNull();
    });

    it('can set the category tree', function () {
        $channel = new Channel('ecommerce')->setCategoryTree('master');
        expect($channel->getCategoryTree())->toBe('master');
    });

    it('can set the category tree to null', function () {
        $channel = new Channel('ecommerce')->setCategoryTree('master')->setCategoryTree(null);
        expect($channel->getCategoryTree())->toBeNull();
    });
});

describe('conversion units management', function () {
    it('can get the conversion units (initially null)', function () {
        $channel = new Channel('ecommerce');
        expect($channel->getConversionUnits())->toBeNull();
    });

    it('can set the conversion units', function () {
        $conversionUnits = ['weight' => 'KILOGRAM', 'length' => 'CENTIMETER'];
        $channel = new Channel('ecommerce')->setConversionUnits($conversionUnits);
        expect($channel->getConversionUnits())->toBe($conversionUnits);
    });

    it('can set the conversion units to null', function () {
        $channel = new Channel('ecommerce')->setConversionUnits(['weight' => 'GRAM'])->setConversionUnits(null);
        expect($channel->getConversionUnits())->toBeNull();
    });
});
