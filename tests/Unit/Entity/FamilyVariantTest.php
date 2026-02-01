<?php

declare(strict_types=1);

use AkeneoLib\Entity\FamilyVariant;

describe('family management', function () {
    it('can get the family', function () {
        $variant = new FamilyVariant('clothing', 'by_size');
        expect($variant->getFamily())->toBe('clothing');
    });

    it('can set the family', function () {
        $variant = new FamilyVariant('old_family', 'by_size')->setFamily('new_family');
        expect($variant->getFamily())->toBe('new_family');
    });
});

describe('code management', function () {
    it('can get the code', function () {
        $variant = new FamilyVariant('clothing', 'by_size');
        expect($variant->getCode())->toBe('by_size');
    });

    it('can set the code', function () {
        $variant = new FamilyVariant('clothing', 'old_code')->setCode('new_code');
        expect($variant->getCode())->toBe('new_code');
    });
});

describe('labels management', function () {
    it('can get the labels (initially null)', function () {
        $variant = new FamilyVariant('clothing', 'by_size');
        expect($variant->getLabels())->toBeNull();
    });

    it('can set the labels', function () {
        $labels = ['en_US' => 'By size', 'fr_FR' => 'Par taille'];
        $variant = new FamilyVariant('clothing', 'by_size')->setLabels($labels);
        expect($variant->getLabels())->toBe($labels);
    });

    it('can set the labels to null', function () {
        $labels = ['en_US' => 'Test'];
        $variant = new FamilyVariant('clothing', 'by_size')->setLabels($labels)->setLabels(null);
        expect($variant->getLabels())->toBeNull();
    });
});

describe('variant attribute sets management', function () {
    it('can get the variant attribute sets (initially null)', function () {
        $variant = new FamilyVariant('clothing', 'by_size');
        expect($variant->getVariantAttributeSets())->toBeNull();
    });

    it('can set the variant attribute sets', function () {
        $sets = [
            ['level' => 1, 'attributes' => ['size']],
            ['level' => 2, 'attributes' => ['color']],
        ];
        $variant = new FamilyVariant('clothing', 'by_size')->setVariantAttributeSets($sets);
        expect($variant->getVariantAttributeSets())->toBe($sets);
    });

    it('can set the variant attribute sets to null', function () {
        $sets = [['level' => 1, 'attributes' => ['size']]];
        $variant = new FamilyVariant('clothing', 'by_size')->setVariantAttributeSets($sets)->setVariantAttributeSets(null);
        expect($variant->getVariantAttributeSets())->toBeNull();
    });
});

describe('common attributes management', function () {
    it('can get the common attributes (initially null)', function () {
        $variant = new FamilyVariant('clothing', 'by_size');
        expect($variant->getCommonAttributes())->toBeNull();
    });

    it('can set the common attributes', function () {
        $attributes = ['name', 'description', 'price'];
        $variant = new FamilyVariant('clothing', 'by_size')->setCommonAttributes($attributes);
        expect($variant->getCommonAttributes())->toBe($attributes);
    });

    it('can set the common attributes to null', function () {
        $attributes = ['name'];
        $variant = new FamilyVariant('clothing', 'by_size')->setCommonAttributes($attributes)->setCommonAttributes(null);
        expect($variant->getCommonAttributes())->toBeNull();
    });
});
