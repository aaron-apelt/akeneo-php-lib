<?php

declare(strict_types=1);

use AkeneoLib\Entity\Asset;
use AkeneoLib\Entity\Value;
use AkeneoLib\Entity\Values;

describe('code management', function () {
    it('can get the code', function () {
        $asset = new Asset('img-001');
        expect($asset->getCode())->toBe('img-001');
    });

    it('can set the code', function () {
        $asset = new Asset('old_code')->setCode('new_code');
        expect($asset->getCode())->toBe('new_code');
    });
});

describe('asset family management', function () {
    it('can get the asset family (initially null)', function () {
        $asset = new Asset('img-001');
        expect($asset->getAssetFamilyCode())->toBeNull();
    });

    it('can set the asset family', function () {
        $asset = new Asset('img-001')->setAssetFamilyCode('packshots');
        expect($asset->getAssetFamilyCode())->toBe('packshots');
    });

    it('can set the asset family to null', function () {
        $asset = new Asset('img-001')->setAssetFamilyCode('packshots')->setAssetFamilyCode(null);
        expect($asset->getAssetFamilyCode())->toBeNull();
    });
});

describe('values management', function () {
    it('can get the values (initially null)', function () {
        $asset = new Asset('img-001');
        expect($asset->getValues())->toBeNull();
    });

    it('can set the values', function () {
        $values = new Values;
        $asset = new Asset('img-001')->setValues($values);
        expect($asset->getValues())->toBe($values);
    });

    it('can set the values to null', function () {
        $asset = new Asset('img-001')->setValues(new Values)->setValues(null);
        expect($asset->getValues())->toBeNull();
    });
});

describe('value management', function () {
    it('can upsert a value and initialize values if null', function () {
        $asset = new Asset('img-001');
        $value = new Value('alt_text', 'Product image');
        $asset->upsertValue($value);
        expect($asset->getValues())->toBeInstanceOf(Values::class)
            ->and($asset->getValue('alt_text'))->toBe($value);
    });

    it('can upsert a value when values already exist', function () {
        $asset = new Asset('img-001');
        $initialValue = new Value('title', 'Initial title');
        $asset->setValues((new Values)->upsert($initialValue));
        $newValue = new Value('title', 'Updated title');
        $asset->upsertValue($newValue);
        expect($asset->getValue('title'))->toBe($newValue);
    });

    it('can get a specific value by code', function () {
        $asset = new Asset('img-001');
        $value1 = new Value('alt_text', 'Image alt');
        $value2 = new Value('title', 'Image title');
        $asset->setValues((new Values)->upsert($value1)->upsert($value2));
        expect($asset->getValue('alt_text'))->toBe($value1)
            ->and($asset->getValue('title'))->toBe($value2)
            ->and($asset->getValue('non_existent'))->toBeNull();
    });

    it('can get a specific value by code, scope, and locale', function () {
        $asset = new Asset('img-001');
        $value1 = new Value('description', 'English desc', 'ecommerce', 'en_US');
        $value2 = new Value('description', 'German desc', 'print', 'de_DE');
        $asset->setValues((new Values)->upsert($value1)->upsert($value2));
        expect($asset->getValue('description', 'ecommerce', 'en_US'))->toBe($value1)
            ->and($asset->getValue('description', 'print', 'de_DE'))->toBe($value2)
            ->and($asset->getValue('description', 'ecommerce', 'de_DE'))->toBeNull();
    });
});
