<?php

declare(strict_types=1);

use AkeneoLib\Entity\AttributeOption;

describe('code management', function () {
    it('can get the code', function () {
        $option = new AttributeOption('red');
        expect($option->getCode())->toBe('red');
    });

    it('can set the code', function () {
        $option = new AttributeOption('old_code')->setCode('new_code');
        expect($option->getCode())->toBe('new_code');
    });
});

describe('labels management', function () {
    it('can get the labels (initially null)', function () {
        $option = new AttributeOption('red');
        expect($option->getLabels())->toBeNull();
    });

    it('can set the labels', function () {
        $labels = ['en_US' => 'Red', 'fr_FR' => 'Rouge'];
        $option = new AttributeOption('red')->setLabels($labels);
        expect($option->getLabels())->toBe($labels);
    });
});

describe('attribute management', function () {
    it('can get the attribute (initially null)', function () {
        $option = new AttributeOption('red');
        expect($option->getAttribute())->toBeNull();
    });

    it('can set the attribute', function () {
        $option = new AttributeOption('red')->setAttribute('color');
        expect($option->getAttribute())->toBe('color');
    });

    it('can set the attribute to null', function () {
        $option = new AttributeOption('red')->setAttribute('color')->setAttribute(null);
        expect($option->getAttribute())->toBeNull();
    });
});

describe('sort order management', function () {
    it('can get the sort order (initially null)', function () {
        $option = new AttributeOption('red');
        expect($option->getSortOrder())->toBeNull();
    });

    it('can set the sort order', function () {
        $option = new AttributeOption('red')->setSortOrder(10);
        expect($option->getSortOrder())->toBe(10);
    });

    it('can set the sort order to null', function () {
        $option = new AttributeOption('red')->setSortOrder(5)->setSortOrder(null);
        expect($option->getSortOrder())->toBeNull();
    });
});
