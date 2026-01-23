<?php

declare(strict_types=1);

use AkeneoLib\Entity\Family;

describe('code management', function () {
    it('can get the code', function () {
        $family = new Family('clothing');
        expect($family->getCode())->toBe('clothing');
    });

    it('can set the code', function () {
        $family = (new Family('old_code'))->setCode('new_code');
        expect($family->getCode())->toBe('new_code');
    });
});

describe('labels management', function () {
    it('can get the labels (initially null)', function () {
        $family = new Family('clothing');
        expect($family->getLabels())->toBeNull();
    });

    it('can set the labels', function () {
        $labels = ['en_US' => 'Clothing', 'fr_FR' => 'Vêtements'];
        $family = (new Family('clothing'))->setLabels($labels);
        expect($family->getLabels())->toBe($labels);
    });

    it('can set the labels to null', function () {
        $labels = ['en_US' => 'Test'];
        $family = (new Family('clothing'))->setLabels($labels)->setLabels(null);
        expect($family->getLabels())->toBeNull();
    });
});

describe('attribute as label management', function () {
    it('can get the attribute as label (initially null)', function () {
        $family = new Family('clothing');
        expect($family->getAttributeAsLabel())->toBeNull();
    });

    it('can set the attribute as label', function () {
        $family = (new Family('clothing'))->setAttributeAsLabel('name');
        expect($family->getAttributeAsLabel())->toBe('name');
    });

    it('can set the attribute as label to null', function () {
        $family = (new Family('clothing'))->setAttributeAsLabel('name')->setAttributeAsLabel(null);
        expect($family->getAttributeAsLabel())->toBeNull();
    });
});

describe('attribute as image management', function () {
    it('can get the attribute as image (initially null)', function () {
        $family = new Family('clothing');
        expect($family->getAttributeAsImage())->toBeNull();
    });

    it('can set the attribute as image', function () {
        $family = (new Family('clothing'))->setAttributeAsImage('picture');
        expect($family->getAttributeAsImage())->toBe('picture');
    });

    it('can set the attribute as image to null', function () {
        $family = (new Family('clothing'))->setAttributeAsImage('picture')->setAttributeAsImage(null);
        expect($family->getAttributeAsImage())->toBeNull();
    });
});

describe('attribute requirements management', function () {
    it('can get the attribute requirements (initially null)', function () {
        $family = new Family('clothing');
        expect($family->getAttributeRequirements())->toBeNull();
    });

    it('can set the attribute requirements', function () {
        $requirements = ['ecommerce' => ['name', 'description']];
        $family = (new Family('clothing'))->setAttributeRequirements($requirements);
        expect($family->getAttributeRequirements())->toBe($requirements);
    });

    it('can set the attribute requirements to null', function () {
        $requirements = ['ecommerce' => ['name']];
        $family = (new Family('clothing'))->setAttributeRequirements($requirements)->setAttributeRequirements(null);
        expect($family->getAttributeRequirements())->toBeNull();
    });
});
