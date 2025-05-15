<?php

declare(strict_types=1);

use AkeneoLib\Entity\Value;
use AkeneoLib\Entity\Values;

it('implements IteratorAggregate and can be iterated', function () {
    $values = new Values;
    $value1 = new Value('name', 'Product 1');
    $value2 = new Value('description', 'A great product');
    $values->upsert($value1)->upsert($value2);

    $iterator = $values->getIterator();
    expect($iterator)->toBeInstanceOf(Generator::class);

    $items = iterator_to_array($iterator, false);
    expect($items)->toHaveCount(2)
        ->and($items[0])->toBe($value1)
        ->and($items[1])->toBe($value2);
});

describe('upsert()', function () {
    it('can upsert a new value', function () {
        $values = new Values;
        $value = new Value('name', 'Product 1');
        $values->upsert($value);
        expect($values->get('name'))->toBe($value);
    });

    it('can update an existing value', function () {
        $values = new Values;
        $initialValue = new Value('name', 'Old Product');
        $updatedValue = new Value('name', 'New Product');
        $values->upsert($initialValue)->upsert($updatedValue);
        expect($values->get('name'))->toBe($updatedValue);
    });

    it('can upsert a value with scope and locale', function () {
        $values = new Values;
        $value1 = new Value('price', 10.99, 'ecommerce', 'en_US');
        $values->upsert($value1);
        expect($values->get('price', 'ecommerce', 'en_US'))->toBe($value1);

        $value2 = new Value('price', 12.50, 'print', 'de_DE');
        $values->upsert($value2);
        expect($values->get('price', 'print', 'de_DE'))->toBe($value2);
    });

    it('can update a value with the same attribute code but different scope/locale', function () {
        $values = new Values;
        $value1 = new Value('price', 10.99, 'ecommerce', 'en_US');
        $value2 = new Value('price', 12.50, 'print', 'de_DE');
        $values->upsert($value1)->upsert($value2);
        expect($values->get('price', 'ecommerce', 'en_US'))->toBe($value1)
            ->and($values->get('price', 'print', 'de_DE'))->toBe($value2);
    });
});

describe('get()', function () {
    it('can get a value by attribute code', function () {
        $values = new Values;
        $value = new Value('name', 'Product 1');
        $values->upsert($value);
        expect($values->get('name'))->toBe($value);
    });

    it('returns null if no value exists for the attribute code', function () {
        $values = new Values;
        expect($values->get('non_existent'))->toBeNull();
    });

    it('can get a value by attribute code, scope, and locale', function () {
        $values = new Values;
        $value = new Value('price', 10.99, 'ecommerce', 'en_US');
        $values->upsert($value);
        expect($values->get('price', 'ecommerce', 'en_US'))->toBe($value);
    });

    it('returns null if no value exists for the specific attribute code, scope, and locale', function () {
        $values = new Values;
        $value = new Value('price', 10.99, 'ecommerce', 'en_US');
        $values->upsert($value);
        expect($values->get('price', 'print', 'de_DE'))->toBeNull();
    });
});

describe('remove()', function () {
    it('can remove a value by attribute code', function () {
        $values = new Values;
        $value = new Value('name', 'Product 1');
        $values->upsert($value)->remove('name');
        expect($values->get('name'))->toBeNull()
            ->and(iterator_to_array($values->getIterator()))->toBeEmpty();
    });

    it('does not remove other values when removing by attribute code', function () {
        $values = new Values;
        $value1 = new Value('name', 'Product 1');
        $value2 = new Value('description', 'A great product');
        $values->upsert($value1)->upsert($value2)->remove('name');
        expect($values->get('description'))->toBe($value2)
            ->and(iterator_to_array($values->getIterator()))->toHaveCount(1);
    });

    it('can remove a value by attribute code, scope, and locale', function () {
        $values = new Values;
        $value1 = new Value('price', 10.99, 'ecommerce', 'en_US');
        $value2 = new Value('price', 12.50, 'print', 'de_DE');
        $values->upsert($value1)->upsert($value2)->remove('price', 'ecommerce', 'en_US');
        expect($values->get('price', 'ecommerce', 'en_US'))->toBeNull()
            ->and($values->get('price', 'print', 'de_DE'))->toBe($value2)
            ->and(iterator_to_array($values->getIterator()))->toHaveCount(1);
    });

    it('does nothing if no value exists for the given attribute code, scope, and locale', function () {
        $values = new Values;
        $value = new Value('name', 'Product 1');
        $values->upsert($value)->remove('price', 'ecommerce', 'en_US');
        expect($values->get('name'))->toBe($value)
            ->and(iterator_to_array($values->getIterator()))->toHaveCount(1);
    });
});
