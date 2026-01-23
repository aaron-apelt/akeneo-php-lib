<?php

declare(strict_types=1);

use AkeneoLib\Entity\AttributeGroup;

describe('code management', function () {
    it('can get the code', function () {
        $group = new AttributeGroup('technical');
        expect($group->getCode())->toBe('technical');
    });

    it('can set the code', function () {
        $group = (new AttributeGroup('old_code'))->setCode('new_code');
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
        $group = (new AttributeGroup('technical'))->setLabels($labels);
        expect($group->getLabels())->toBe($labels);
    });

    it('can set the labels to null', function () {
        $labels = ['en_US' => 'Test'];
        $group = (new AttributeGroup('technical'))->setLabels($labels)->setLabels(null);
        expect($group->getLabels())->toBeNull();
    });
});
