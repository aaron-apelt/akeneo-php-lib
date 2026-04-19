<?php

declare(strict_types=1);

use AkeneoLib\Entity\Product;
use AkeneoLib\Entity\Value;
use AkeneoLib\Entity\Values;

describe('value management', function () {
    it('can upsert a value and initialize values if null', function () {
        $product = new Product('id');
        $value = new Value('name', 'My Product');
        $product->upsertValue($value);
        expect($product->getValues())->toBeInstanceOf(Values::class)
            ->and($product->getValue('name'))->toBe($value);
    });

    it('can upsert a value when values already exist', function () {
        $product = new Product('id');
        $initialValue = new Value('description', 'Initial description');
        $product->setValues((new Values)->upsert($initialValue));
        $newValue = new Value('description', 'Updated description');
        $product->upsertValue($newValue);
        expect($product->getValue('description'))->toBe($newValue);
    });

    it('can get a specific value by code', function () {
        $product = new Product('id');
        $value1 = new Value('color', 'red');
        $value2 = new Value('size', 'M');
        $product->setValues((new Values)->upsert($value1)->upsert($value2));
        expect($product->getValue('color'))->toBe($value1)
            ->and($product->getValue('size'))->toBe($value2)
            ->and($product->getValue('non_existent'))->toBeNull();
    });

    it('can get a specific value by code, scope, and locale', function () {
        $product = new Product('id');
        $value1 = new Value('price', 10.99, 'ecommerce', 'en_US');
        $value2 = new Value('price', 12.50, 'print', 'de_DE');
        $product->setValues((new Values)->upsert($value1)->upsert($value2));
        expect($product->getValue('price', 'ecommerce', 'en_US'))->toBe($value1)
            ->and($product->getValue('price', 'print', 'de_DE'))->toBe($value2)
            ->and($product->getValue('price', 'ecommerce', 'de_DE'))->toBeNull();
    });
});
