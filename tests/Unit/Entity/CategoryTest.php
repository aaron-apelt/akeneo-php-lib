<?php

declare(strict_types=1);

use AkeneoLib\Entity\Category;

describe('code management', function () {
    it('can get the code', function () {
        $category = new Category('master');
        expect($category->getCode())->toBe('master');
    });

    it('can set the code', function () {
        $category = new Category('old_code')->setCode('new_code');
        expect($category->getCode())->toBe('new_code');
    });
});

describe('parent management', function () {
    it('can get the parent (initially null)', function () {
        $category = new Category('child');
        expect($category->getParent())->toBeNull();
    });

    it('can get the parent when set via constructor', function () {
        $category = new Category('child', 'master');
        expect($category->getParent())->toBe('master');
    });

    it('can set the parent', function () {
        $category = new Category('child')->setParent('master');
        expect($category->getParent())->toBe('master');
    });

    it('can set the parent to null', function () {
        $category = new Category('child', 'master')->setParent(null);
        expect($category->getParent())->toBeNull();
    });
});

describe('labels management', function () {
    it('can get the labels (initially null)', function () {
        $category = new Category('cat');
        expect($category->getLabels())->toBeNull();
    });

    it('can get the labels when set via constructor', function () {
        $labels = ['en_US' => 'Category', 'de_DE' => 'Kategorie'];
        $category = new Category('cat', null, $labels);
        expect($category->getLabels())->toBe($labels);
    });

    it('can set the labels', function () {
        $labels = ['en_US' => 'Products', 'fr_FR' => 'Produits'];
        $category = new Category('cat')->setLabels($labels);
        expect($category->getLabels())->toBe($labels);
    });

    it('can set the labels to null', function () {
        $labels = ['en_US' => 'Test'];
        $category = new Category('cat', null, $labels)->setLabels(null);
        expect($category->getLabels())->toBeNull();
    });
});
