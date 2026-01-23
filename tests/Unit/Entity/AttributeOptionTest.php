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
