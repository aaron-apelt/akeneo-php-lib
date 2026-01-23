<?php

declare(strict_types=1);

use AkeneoLib\Entity\ReferenceEntityRecord;
use AkeneoLib\Entity\Value;
use AkeneoLib\Entity\Values;

describe('code management', function () {
    it('can get the code', function () {
        $record = new ReferenceEntityRecord('nike');
        expect($record->getCode())->toBe('nike');
    });

    it('can set the code', function () {
        $record = (new ReferenceEntityRecord('old_code'))->setCode('new_code');
        expect($record->getCode())->toBe('new_code');
    });
});

describe('values management', function () {
    it('can get the values (initially null)', function () {
        $record = new ReferenceEntityRecord('nike');
        expect($record->getValues())->toBeNull();
    });

    it('can set the values', function () {
        $values = new Values;
        $record = (new ReferenceEntityRecord('nike'))->setValues($values);
        expect($record->getValues())->toBe($values);
    });

    it('can set the values to null', function () {
        $record = (new ReferenceEntityRecord('nike'))->setValues(new Values)->setValues(null);
        expect($record->getValues())->toBeNull();
    });
});

describe('value management', function () {
    it('can upsert a value and initialize values if null', function () {
        $record = new ReferenceEntityRecord('nike');
        $value = new Value('name', 'Nike Inc.');
        $record->upsertValue($value);
        expect($record->getValues())->toBeInstanceOf(Values::class)
            ->and($record->getValue('name'))->toBe($value);
    });

    it('can upsert a value when values already exist', function () {
        $record = new ReferenceEntityRecord('nike');
        $initialValue = new Value('description', 'Initial description');
        $record->setValues((new Values)->upsert($initialValue));
        $newValue = new Value('description', 'Updated description');
        $record->upsertValue($newValue);
        expect($record->getValue('description'))->toBe($newValue);
    });

    it('can get a specific value by code', function () {
        $record = new ReferenceEntityRecord('nike');
        $value1 = new Value('name', 'Nike');
        $value2 = new Value('country', 'USA');
        $record->setValues((new Values)->upsert($value1)->upsert($value2));
        expect($record->getValue('name'))->toBe($value1)
            ->and($record->getValue('country'))->toBe($value2)
            ->and($record->getValue('non_existent'))->toBeNull();
    });

    it('can get a specific value by code, scope, and locale', function () {
        $record = new ReferenceEntityRecord('nike');
        $value1 = new Value('description', 'English description', 'ecommerce', 'en_US');
        $value2 = new Value('description', 'German description', 'print', 'de_DE');
        $record->setValues((new Values)->upsert($value1)->upsert($value2));
        expect($record->getValue('description', 'ecommerce', 'en_US'))->toBe($value1)
            ->and($record->getValue('description', 'print', 'de_DE'))->toBe($value2)
            ->and($record->getValue('description', 'ecommerce', 'de_DE'))->toBeNull();
    });
});
