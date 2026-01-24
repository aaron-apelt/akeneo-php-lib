<?php

declare(strict_types=1);

use AkeneoLib\Entity\Family;

describe('code management', function () {
    it('can get the code', function () {
        $family = new Family('clothing');
        expect($family->getCode())->toBe('clothing');
    });

    it('can set the code', function () {
        $family = new Family('old_code')->setCode('new_code');
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
        $family = new Family('clothing')->setLabels($labels);
        expect($family->getLabels())->toBe($labels);
    });

    it('can set the labels to null', function () {
        $labels = ['en_US' => 'Test'];
        $family = new Family('clothing')->setLabels($labels)->setLabels(null);
        expect($family->getLabels())->toBeNull();
    });
});

describe('attributes management', function () {
    it('can get the attributes (initially null)', function () {
        $family = new Family('clothing');
        expect($family->getAttributes())->toBeNull();
    });

    it('can set the attributes', function () {
        $attributes = ['name', 'description', 'price'];
        $family = new Family('clothing')->setAttributes($attributes);
        expect($family->getAttributes())->toBe($attributes);
    });

    it('can set the attributes to null', function () {
        $attributes = ['name'];
        $family = new Family('clothing')->setAttributes($attributes)->setAttributes(null);
        expect($family->getAttributes())->toBeNull();
    });
});

describe('attribute as label management', function () {
    it('can get the attribute as label (initially null)', function () {
        $family = new Family('clothing');
        expect($family->getAttributeAsLabel())->toBeNull();
    });

    it('can set the attribute as label', function () {
        $family = new Family('clothing')->setAttributeAsLabel('name');
        expect($family->getAttributeAsLabel())->toBe('name');
    });

    it('can set the attribute as label to null', function () {
        $family = new Family('clothing')->setAttributeAsLabel('name')->setAttributeAsLabel(null);
        expect($family->getAttributeAsLabel())->toBeNull();
    });
});

describe('attribute as image management', function () {
    it('can get the attribute as image (initially null)', function () {
        $family = new Family('clothing');
        expect($family->getAttributeAsImage())->toBeNull();
    });

    it('can set the attribute as image', function () {
        $family = new Family('clothing')->setAttributeAsImage('picture');
        expect($family->getAttributeAsImage())->toBe('picture');
    });

    it('can set the attribute as image to null', function () {
        $family = new Family('clothing')->setAttributeAsImage('picture')->setAttributeAsImage(null);
        expect($family->getAttributeAsImage())->toBeNull();
    });
});

describe('attribute as main media management', function () {
    it('can get the attribute as main media (initially null)', function () {
        $family = new Family('clothing');
        expect($family->getAttributeAsMainMedia())->toBeNull();
    });

    it('can set the attribute as main media', function () {
        $family = new Family('clothing')->setAttributeAsMainMedia('main_image');
        expect($family->getAttributeAsMainMedia())->toBe('main_image');
    });

    it('can set the attribute as main media to null', function () {
        $family = new Family('clothing')->setAttributeAsMainMedia('main_image')->setAttributeAsMainMedia(null);
        expect($family->getAttributeAsMainMedia())->toBeNull();
    });
});

describe('attribute requirements management', function () {
    it('can get the attribute requirements (initially null)', function () {
        $family = new Family('clothing');
        expect($family->getAttributeRequirements())->toBeNull();
    });

    it('can set the attribute requirements', function () {
        $requirements = ['ecommerce' => ['name', 'description']];
        $family = new Family('clothing')->setAttributeRequirements($requirements);
        expect($family->getAttributeRequirements())->toBe($requirements);
    });

    it('can set the attribute requirements to null', function () {
        $requirements = ['ecommerce' => ['name']];
        $family = new Family('clothing')->setAttributeRequirements($requirements)->setAttributeRequirements(null);
        expect($family->getAttributeRequirements())->toBeNull();
    });
});

describe('parent management', function () {
    it('can get the parent (initially null)', function () {
        $family = new Family('clothing');
        expect($family->getParent())->toBeNull();
    });

    it('can set the parent', function () {
        $family = new Family('clothing')->setParent('master_family');
        expect($family->getParent())->toBe('master_family');
    });

    it('can set the parent to null', function () {
        $family = new Family('clothing')->setParent('master_family')->setParent(null);
        expect($family->getParent())->toBeNull();
    });
});
