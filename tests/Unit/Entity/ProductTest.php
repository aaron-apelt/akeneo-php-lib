<?php

declare(strict_types=1);

use AkeneoLib\Entity\Product;
use AkeneoLib\Entity\Value;
use AkeneoLib\Entity\Values;

describe('identifier management', function () {
    it('can get the identifier', function () {
        $product = new Product('initial_id');
        expect($product->getIdentifier())->toBe('initial_id');
    });

    it('can set the identifier', function () {
        $product = (new Product('old_id'))->setIdentifier('new_id');
        expect($product->getIdentifier())->toBe('new_id');
    });
});

describe('enabled status management', function () {
    it('can get the enabled status (initially null)', function () {
        $product = new Product('id');
        expect($product->isEnabled())->toBeNull();
    });

    it('can set the enabled status to true', function () {
        $product = (new Product('id'))->setEnabled(true);
        expect($product->isEnabled())->toBeTrue();
    });

    it('can set the enabled status to false', function () {
        $product = (new Product('id'))->setEnabled(false);
        expect($product->isEnabled())->toBeFalse();
    });

    it('can set the enabled status to null', function () {
        $product = (new Product('id'))->setEnabled(true)->setEnabled(null);
        expect($product->isEnabled())->toBeNull();
    });
});

describe('family management', function () {
    it('can get the family (initially null)', function () {
        $product = new Product('id');
        expect($product->getFamily())->toBeNull();
    });

    it('can set the family', function () {
        $product = (new Product('id'))->setFamily('clothing');
        expect($product->getFamily())->toBe('clothing');
    });

    it('can set the family to null', function () {
        $product = (new Product('id'))->setFamily('electronics')->setFamily(null);
        expect($product->getFamily())->toBeNull();
    });
});

describe('categories management', function () {
    it('can get the categories (initially null)', function () {
        $product = new Product('id');
        expect($product->getCategories())->toBeNull();
    });

    it('can set the categories', function () {
        $categories = ['t-shirts', 'summer'];
        $product = (new Product('id'))->setCategories($categories);
        expect($product->getCategories())->toBe($categories);
    });

    it('can set the categories to null', function () {
        $product = (new Product('id'))->setCategories(['shoes'])->setCategories(null);
        expect($product->getCategories())->toBeNull();
    });
});

describe('parent management', function () {
    it('can get the parent (initially null)', function () {
        $product = new Product('id');
        expect($product->getParent())->toBeNull();
    });

    it('can set the parent', function () {
        $product = (new Product('id'))->setParent('master_product');
        expect($product->getParent())->toBe('master_product');
    });

    it('can set the parent to null', function () {
        $product = (new Product('id'))->setParent('another_master')->setParent(null);
        expect($product->getParent())->toBeNull();
    });
});

describe('values management', function () {
    it('can get the values (initially null)', function () {
        $product = new Product('id');
        expect($product->getValues())->toBeNull();
    });

    it('can set the values', function () {
        $values = new Values;
        $product = (new Product('id'))->setValues($values);
        expect($product->getValues())->toBe($values);
    });

    it('can set the values to null', function () {
        $product = (new Product('id'))->setValues(new Values)->setValues(null);
        expect($product->getValues())->toBeNull();
    });
});

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
