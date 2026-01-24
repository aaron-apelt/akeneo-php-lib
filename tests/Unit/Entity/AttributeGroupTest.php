<?php

declare(strict_types=1);

use AkeneoLib\Entity\AttributeGroup;

describe('code management', function () {
    it('can get the code', function () {
        $group = new AttributeGroup('technical');
        expect($group->getCode())->toBe('technical');
    });

    it('can set the code', function () {
        $group = new AttributeGroup('old_code')->setCode('new_code');
        expect($group->getCode())->toBe('new_code');
    });
});

describe('labels management', function () {
    it('can get the labels (initially null)', function () {
        $group = new AttributeGroup('technical');
        expect($group->getLabels())->toBeNull();
    });

    it('can set the labels', function () {
        $labels = ['en_US' => 'Technical', 'fr_FR' => 'Technique'];
        $group = new AttributeGroup('technical')->setLabels($labels);
        expect($group->getLabels())->toBe($labels);
    });

    it('can set the labels to null', function () {
        $labels = ['en_US' => 'Test'];
        $group = new AttributeGroup('technical')->setLabels($labels)->setLabels(null);
        expect($group->getLabels())->toBeNull();
    });
});

describe('sort order management', function () {
    it('can get the sort order (initially null)', function () {
        $group = new AttributeGroup('technical');
        expect($group->getSortOrder())->toBeNull();
    });

    it('can set the sort order', function () {
        $group = new AttributeGroup('technical')->setSortOrder(2);
        expect($group->getSortOrder())->toBe(2);
    });

    it('can set the sort order to null', function () {
        $group = new AttributeGroup('technical')->setSortOrder(5)->setSortOrder(null);
        expect($group->getSortOrder())->toBeNull();
    });
});

describe('attributes management', function () {
    it('can get the attributes (initially null)', function () {
        $group = new AttributeGroup('technical');
        expect($group->getAttributes())->toBeNull();
    });

    it('can set the attributes', function () {
        $attributes = ['meta_title', 'meta_description', 'seo_keywords'];
        $group = new AttributeGroup('technical')->setAttributes($attributes);
        expect($group->getAttributes())->toBe($attributes);
    });

    it('can set the attributes to null', function () {
        $group = new AttributeGroup('technical')->setAttributes(['name'])->setAttributes(null);
        expect($group->getAttributes())->toBeNull();
    });
});
