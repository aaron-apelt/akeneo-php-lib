<?php

declare(strict_types=1);

use AkeneoLib\Entity\Attribute;

describe('code management', function () {
    it('can get the code', function () {
        $attribute = new Attribute('color');
        expect($attribute->getCode())->toBe('color');
    });

    it('can set the code', function () {
        $attribute = new Attribute('old_code')->setCode('new_code');
        expect($attribute->getCode())->toBe('new_code');
    });
});

describe('type management', function () {
    it('can get the type (initially null)', function () {
        $attribute = new Attribute('color');
        expect($attribute->getType())->toBeNull();
    });

    it('can set the type', function () {
        $attribute = new Attribute('color')->setType('pim_catalog_simpleselect');
        expect($attribute->getType())->toBe('pim_catalog_simpleselect');
    });
});

describe('scopable management', function () {
    it('can check if scopable (initially null)', function () {
        $attribute = new Attribute('color');
        expect($attribute->isScopable())->toBeNull();
    });

    it('can set scopable to true', function () {
        $attribute = new Attribute('color')->setScopable(true);
        expect($attribute->isScopable())->toBeTrue();
    });

    it('can set scopable to false', function () {
        $attribute = new Attribute('color')->setScopable(false);
        expect($attribute->isScopable())->toBeFalse();
    });
});

describe('localizable management', function () {
    it('can check if localizable (initially null)', function () {
        $attribute = new Attribute('color');
        expect($attribute->isLocalizable())->toBeNull();
    });

    it('can set localizable to true', function () {
        $attribute = new Attribute('description')->setLocalizable(true);
        expect($attribute->isLocalizable())->toBeTrue();
    });

    it('can set localizable to false', function () {
        $attribute = new Attribute('sku')->setLocalizable(false);
        expect($attribute->isLocalizable())->toBeFalse();
    });
});

describe('default metric unit management', function () {
    it('can get the default metric unit (initially null)', function () {
        $attribute = new Attribute('weight');
        expect($attribute->getDefaultMetricUnit())->toBeNull();
    });

    it('can set the default metric unit', function () {
        $attribute = new Attribute('weight')->setDefaultMetricUnit('KILOGRAM');
        expect($attribute->getDefaultMetricUnit())->toBe('KILOGRAM');
    });

    it('can set the default metric unit to null', function () {
        $attribute = new Attribute('weight')->setDefaultMetricUnit('KILOGRAM')->setDefaultMetricUnit(null);
        expect($attribute->getDefaultMetricUnit())->toBeNull();
    });
});
