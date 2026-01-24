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

describe('labels management', function () {
    it('can get the labels (initially null)', function () {
        $attribute = new Attribute('color');
        expect($attribute->getLabels())->toBeNull();
    });

    it('can set the labels', function () {
        $labels = ['en_US' => 'Color', 'fr_FR' => 'Couleur'];
        $attribute = new Attribute('color')->setLabels($labels);
        expect($attribute->getLabels())->toBe($labels);
    });

    it('can set the labels to null', function () {
        $attribute = new Attribute('color')->setLabels(['en_US' => 'Color'])->setLabels(null);
        expect($attribute->getLabels())->toBeNull();
    });
});

describe('group management', function () {
    it('can get the group (initially null)', function () {
        $attribute = new Attribute('color');
        expect($attribute->getGroup())->toBeNull();
    });

    it('can set the group', function () {
        $attribute = new Attribute('color')->setGroup('marketing');
        expect($attribute->getGroup())->toBe('marketing');
    });

    it('can set the group to null', function () {
        $attribute = new Attribute('color')->setGroup('marketing')->setGroup(null);
        expect($attribute->getGroup())->toBeNull();
    });
});

describe('unique management', function () {
    it('can check if unique (initially null)', function () {
        $attribute = new Attribute('sku');
        expect($attribute->isUnique())->toBeNull();
    });

    it('can set unique to true', function () {
        $attribute = new Attribute('sku')->setUnique(true);
        expect($attribute->isUnique())->toBeTrue();
    });

    it('can set unique to false', function () {
        $attribute = new Attribute('color')->setUnique(false);
        expect($attribute->isUnique())->toBeFalse();
    });

    it('can set unique to null', function () {
        $attribute = new Attribute('sku')->setUnique(true)->setUnique(null);
        expect($attribute->isUnique())->toBeNull();
    });
});

describe('useable as grid filter management', function () {
    it('can check if useable as grid filter (initially null)', function () {
        $attribute = new Attribute('color');
        expect($attribute->isUseableAsGridFilter())->toBeNull();
    });

    it('can set useable as grid filter to true', function () {
        $attribute = new Attribute('color')->setUseableAsGridFilter(true);
        expect($attribute->isUseableAsGridFilter())->toBeTrue();
    });

    it('can set useable as grid filter to false', function () {
        $attribute = new Attribute('internal_note')->setUseableAsGridFilter(false);
        expect($attribute->isUseableAsGridFilter())->toBeFalse();
    });

    it('can set useable as grid filter to null', function () {
        $attribute = new Attribute('color')->setUseableAsGridFilter(true)->setUseableAsGridFilter(null);
        expect($attribute->isUseableAsGridFilter())->toBeNull();
    });
});

describe('available locales management', function () {
    it('can get the available locales (initially null)', function () {
        $attribute = new Attribute('description');
        expect($attribute->getAvailableLocales())->toBeNull();
    });

    it('can set the available locales', function () {
        $locales = ['en_US', 'fr_FR', 'de_DE'];
        $attribute = new Attribute('description')->setAvailableLocales($locales);
        expect($attribute->getAvailableLocales())->toBe($locales);
    });

    it('can set the available locales to null', function () {
        $attribute = new Attribute('description')->setAvailableLocales(['en_US'])->setAvailableLocales(null);
        expect($attribute->getAvailableLocales())->toBeNull();
    });
});

describe('max characters management', function () {
    it('can get the max characters (initially null)', function () {
        $attribute = new Attribute('name');
        expect($attribute->getMaxCharacters())->toBeNull();
    });

    it('can set the max characters', function () {
        $attribute = new Attribute('name')->setMaxCharacters(255);
        expect($attribute->getMaxCharacters())->toBe(255);
    });

    it('can set the max characters to null', function () {
        $attribute = new Attribute('name')->setMaxCharacters(100)->setMaxCharacters(null);
        expect($attribute->getMaxCharacters())->toBeNull();
    });
});

describe('validation rule management', function () {
    it('can get the validation rule (initially null)', function () {
        $attribute = new Attribute('email');
        expect($attribute->getValidationRule())->toBeNull();
    });

    it('can set the validation rule', function () {
        $attribute = new Attribute('email')->setValidationRule('email');
        expect($attribute->getValidationRule())->toBe('email');
    });

    it('can set the validation rule to null', function () {
        $attribute = new Attribute('email')->setValidationRule('email')->setValidationRule(null);
        expect($attribute->getValidationRule())->toBeNull();
    });
});

describe('validation regexp management', function () {
    it('can get the validation regexp (initially null)', function () {
        $attribute = new Attribute('code');
        expect($attribute->getValidationRegexp())->toBeNull();
    });

    it('can set the validation regexp', function () {
        $attribute = new Attribute('code')->setValidationRegexp('^[A-Z]{3}$');
        expect($attribute->getValidationRegexp())->toBe('^[A-Z]{3}$');
    });

    it('can set the validation regexp to null', function () {
        $attribute = new Attribute('code')->setValidationRegexp('^[A-Z]$')->setValidationRegexp(null);
        expect($attribute->getValidationRegexp())->toBeNull();
    });
});

describe('wysiwyg enabled management', function () {
    it('can check if wysiwyg enabled (initially null)', function () {
        $attribute = new Attribute('description');
        expect($attribute->isWysiwygEnabled())->toBeNull();
    });

    it('can set wysiwyg enabled to true', function () {
        $attribute = new Attribute('description')->setWysiwygEnabled(true);
        expect($attribute->isWysiwygEnabled())->toBeTrue();
    });

    it('can set wysiwyg enabled to false', function () {
        $attribute = new Attribute('description')->setWysiwygEnabled(false);
        expect($attribute->isWysiwygEnabled())->toBeFalse();
    });

    it('can set wysiwyg enabled to null', function () {
        $attribute = new Attribute('description')->setWysiwygEnabled(true)->setWysiwygEnabled(null);
        expect($attribute->isWysiwygEnabled())->toBeNull();
    });
});

describe('number min management', function () {
    it('can get the number min (initially null)', function () {
        $attribute = new Attribute('quantity');
        expect($attribute->getNumberMin())->toBeNull();
    });

    it('can set the number min', function () {
        $attribute = new Attribute('quantity')->setNumberMin(0);
        expect($attribute->getNumberMin())->toBe(0);
    });

    it('can set the number min to null', function () {
        $attribute = new Attribute('quantity')->setNumberMin(10)->setNumberMin(null);
        expect($attribute->getNumberMin())->toBeNull();
    });
});

describe('number max management', function () {
    it('can get the number max (initially null)', function () {
        $attribute = new Attribute('quantity');
        expect($attribute->getNumberMax())->toBeNull();
    });

    it('can set the number max', function () {
        $attribute = new Attribute('quantity')->setNumberMax(9999);
        expect($attribute->getNumberMax())->toBe(9999);
    });

    it('can set the number max to null', function () {
        $attribute = new Attribute('quantity')->setNumberMax(100)->setNumberMax(null);
        expect($attribute->getNumberMax())->toBeNull();
    });
});

describe('decimals allowed management', function () {
    it('can check if decimals allowed (initially null)', function () {
        $attribute = new Attribute('price');
        expect($attribute->isDecimalsAllowed())->toBeNull();
    });

    it('can set decimals allowed to true', function () {
        $attribute = new Attribute('price')->setDecimalsAllowed(true);
        expect($attribute->isDecimalsAllowed())->toBeTrue();
    });

    it('can set decimals allowed to false', function () {
        $attribute = new Attribute('quantity')->setDecimalsAllowed(false);
        expect($attribute->isDecimalsAllowed())->toBeFalse();
    });

    it('can set decimals allowed to null', function () {
        $attribute = new Attribute('price')->setDecimalsAllowed(true)->setDecimalsAllowed(null);
        expect($attribute->isDecimalsAllowed())->toBeNull();
    });
});

describe('negative allowed management', function () {
    it('can check if negative allowed (initially null)', function () {
        $attribute = new Attribute('temperature');
        expect($attribute->isNegativeAllowed())->toBeNull();
    });

    it('can set negative allowed to true', function () {
        $attribute = new Attribute('temperature')->setNegativeAllowed(true);
        expect($attribute->isNegativeAllowed())->toBeTrue();
    });

    it('can set negative allowed to false', function () {
        $attribute = new Attribute('price')->setNegativeAllowed(false);
        expect($attribute->isNegativeAllowed())->toBeFalse();
    });

    it('can set negative allowed to null', function () {
        $attribute = new Attribute('temperature')->setNegativeAllowed(true)->setNegativeAllowed(null);
        expect($attribute->isNegativeAllowed())->toBeNull();
    });
});

describe('date min management', function () {
    it('can get the date min (initially null)', function () {
        $attribute = new Attribute('release_date');
        expect($attribute->getDateMin())->toBeNull();
    });

    it('can set the date min', function () {
        $attribute = new Attribute('release_date')->setDateMin('2020-01-01');
        expect($attribute->getDateMin())->toBe('2020-01-01');
    });

    it('can set the date min to null', function () {
        $attribute = new Attribute('release_date')->setDateMin('2020-01-01')->setDateMin(null);
        expect($attribute->getDateMin())->toBeNull();
    });
});

describe('date max management', function () {
    it('can get the date max (initially null)', function () {
        $attribute = new Attribute('expiry_date');
        expect($attribute->getDateMax())->toBeNull();
    });

    it('can set the date max', function () {
        $attribute = new Attribute('expiry_date')->setDateMax('2030-12-31');
        expect($attribute->getDateMax())->toBe('2030-12-31');
    });

    it('can set the date max to null', function () {
        $attribute = new Attribute('expiry_date')->setDateMax('2030-12-31')->setDateMax(null);
        expect($attribute->getDateMax())->toBeNull();
    });
});

describe('max file size management', function () {
    it('can get the max file size (initially null)', function () {
        $attribute = new Attribute('product_image');
        expect($attribute->getMaxFileSize())->toBeNull();
    });

    it('can set the max file size', function () {
        $attribute = new Attribute('product_image')->setMaxFileSize('10485760');
        expect($attribute->getMaxFileSize())->toBe('10485760');
    });

    it('can set the max file size to null', function () {
        $attribute = new Attribute('product_image')->setMaxFileSize('1024')->setMaxFileSize(null);
        expect($attribute->getMaxFileSize())->toBeNull();
    });
});

describe('allowed extensions management', function () {
    it('can get the allowed extensions (initially null)', function () {
        $attribute = new Attribute('product_image');
        expect($attribute->getAllowedExtensions())->toBeNull();
    });

    it('can set the allowed extensions', function () {
        $extensions = ['jpg', 'png', 'gif'];
        $attribute = new Attribute('product_image')->setAllowedExtensions($extensions);
        expect($attribute->getAllowedExtensions())->toBe($extensions);
    });

    it('can set the allowed extensions to null', function () {
        $attribute = new Attribute('product_image')->setAllowedExtensions(['jpg'])->setAllowedExtensions(null);
        expect($attribute->getAllowedExtensions())->toBeNull();
    });
});

describe('metric family management', function () {
    it('can get the metric family (initially null)', function () {
        $attribute = new Attribute('weight');
        expect($attribute->getMetricFamily())->toBeNull();
    });

    it('can set the metric family', function () {
        $attribute = new Attribute('weight')->setMetricFamily('Weight');
        expect($attribute->getMetricFamily())->toBe('Weight');
    });

    it('can set the metric family to null', function () {
        $attribute = new Attribute('weight')->setMetricFamily('Weight')->setMetricFamily(null);
        expect($attribute->getMetricFamily())->toBeNull();
    });
});

describe('reference data name management', function () {
    it('can get the reference data name (initially null)', function () {
        $attribute = new Attribute('brand');
        expect($attribute->getReferenceDataName())->toBeNull();
    });

    it('can set the reference data name', function () {
        $attribute = new Attribute('brand')->setReferenceDataName('brand');
        expect($attribute->getReferenceDataName())->toBe('brand');
    });

    it('can set the reference data name to null', function () {
        $attribute = new Attribute('brand')->setReferenceDataName('brand')->setReferenceDataName(null);
        expect($attribute->getReferenceDataName())->toBeNull();
    });
});

describe('sort order management', function () {
    it('can get the sort order (initially null)', function () {
        $attribute = new Attribute('color');
        expect($attribute->getSortOrder())->toBeNull();
    });

    it('can set the sort order', function () {
        $attribute = new Attribute('color')->setSortOrder(10);
        expect($attribute->getSortOrder())->toBe(10);
    });

    it('can set the sort order to null', function () {
        $attribute = new Attribute('color')->setSortOrder(5)->setSortOrder(null);
        expect($attribute->getSortOrder())->toBeNull();
    });
});

describe('default value management', function () {
    it('can check if has default value (initially false)', function () {
        $attribute = new Attribute('enabled');
        expect($attribute->hasDefaultValue())->toBeFalse();
    });

    it('can set default value to true', function () {
        $attribute = new Attribute('enabled')->setDefaultValue(true);
        expect($attribute->hasDefaultValue())->toBeTrue();
    });

    it('can set default value to false', function () {
        $attribute = new Attribute('enabled')->setDefaultValue(false);
        expect($attribute->hasDefaultValue())->toBeTrue();
    });

    it('can set default value to null', function () {
        $attribute = new Attribute('enabled')->setDefaultValue(true)->setDefaultValue(null);
        expect($attribute->hasDefaultValue())->toBeFalse();
    });
});

describe('main identifier management', function () {
    it('can check if main identifier (initially null)', function () {
        $attribute = new Attribute('sku');
        expect($attribute->isMainIdentifier())->toBeNull();
    });

    it('can set main identifier to true', function () {
        $attribute = new Attribute('sku')->setIsMainIdentifer(true);
        expect($attribute->isMainIdentifier())->toBeTrue();
    });

    it('can set main identifier to false', function () {
        $attribute = new Attribute('color')->setIsMainIdentifer(false);
        expect($attribute->isMainIdentifier())->toBeFalse();
    });

    it('can set main identifier to null', function () {
        $attribute = new Attribute('sku')->setIsMainIdentifer(true)->setIsMainIdentifer(null);
        expect($attribute->isMainIdentifier())->toBeNull();
    });
});

describe('mandatory management', function () {
    it('can check if mandatory (initially null)', function () {
        $attribute = new Attribute('name');
        expect($attribute->isMandatory())->toBeNull();
    });

    it('can set mandatory to true', function () {
        $attribute = new Attribute('name')->setMandatory(true);
        expect($attribute->isMandatory())->toBeTrue();
    });

    it('can set mandatory to false', function () {
        $attribute = new Attribute('description')->setMandatory(false);
        expect($attribute->isMandatory())->toBeFalse();
    });

    it('can set mandatory to null', function () {
        $attribute = new Attribute('name')->setMandatory(true)->setMandatory(null);
        expect($attribute->isMandatory())->toBeNull();
    });
});

describe('decimal places strategy management', function () {
    it('can get the decimal places strategy (initially null)', function () {
        $attribute = new Attribute('price');
        expect($attribute->getDecimalPlacesStrategy())->toBeNull();
    });

    it('can set the decimal places strategy', function () {
        $attribute = new Attribute('price')->setDecimalPlacesStrategy('fixed');
        expect($attribute->getDecimalPlacesStrategy())->toBe('fixed');
    });

    it('can set the decimal places strategy to null', function () {
        $attribute = new Attribute('price')->setDecimalPlacesStrategy('fixed')->setDecimalPlacesStrategy(null);
        expect($attribute->getDecimalPlacesStrategy())->toBeNull();
    });
});

describe('decimal places management', function () {
    it('can get the decimal places (initially null)', function () {
        $attribute = new Attribute('price');
        expect($attribute->getDecimalPlaces())->toBeNull();
    });

    it('can set the decimal places', function () {
        $attribute = new Attribute('price')->setDecimalPlaces(2);
        expect($attribute->getDecimalPlaces())->toBe(2);
    });

    it('can set the decimal places to null', function () {
        $attribute = new Attribute('price')->setDecimalPlaces(4)->setDecimalPlaces(null);
        expect($attribute->getDecimalPlaces())->toBeNull();
    });
});

describe('enable option creation during import management', function () {
    it('can get enable option creation during import (initially null)', function () {
        $attribute = new Attribute('color');
        expect($attribute->getEnableOptionCreationDuringImport())->toBeNull();
    });

    it('can set enable option creation during import to true', function () {
        $attribute = new Attribute('color')->setEnableOptionCreationDuringImport(true);
        expect($attribute->getEnableOptionCreationDuringImport())->toBeTrue();
    });

    it('can set enable option creation during import to false', function () {
        $attribute = new Attribute('color')->setEnableOptionCreationDuringImport(false);
        expect($attribute->getEnableOptionCreationDuringImport())->toBeFalse();
    });

    it('can set enable option creation during import to null', function () {
        $attribute = new Attribute('color')->setEnableOptionCreationDuringImport(true)->setEnableOptionCreationDuringImport(null);
        expect($attribute->getEnableOptionCreationDuringImport())->toBeNull();
    });
});

describe('max items count management', function () {
    it('can get the max items count (initially null)', function () {
        $attribute = new Attribute('tags');
        expect($attribute->getMaxItemsCount())->toBeNull();
    });

    it('can set the max items count', function () {
        $attribute = new Attribute('tags')->setMaxItemsCount(10);
        expect($attribute->getMaxItemsCount())->toBe(10);
    });

    it('can set the max items count to null', function () {
        $attribute = new Attribute('tags')->setMaxItemsCount(5)->setMaxItemsCount(null);
        expect($attribute->getMaxItemsCount())->toBeNull();
    });
});
