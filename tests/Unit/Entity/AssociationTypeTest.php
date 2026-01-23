<?php

declare(strict_types=1);

use AkeneoLib\Entity\AssociationType;

describe('code management', function () {
    it('can get the code', function () {
        $type = new AssociationType('X_SELL');
        expect($type->getCode())->toBe('X_SELL');
    });

    it('can set the code', function () {
        $type = (new AssociationType('old_code'))->setCode('new_code');
        expect($type->getCode())->toBe('new_code');
    });
});

describe('labels management', function () {
    it('can get the labels (initially null)', function () {
        $type = new AssociationType('X_SELL');
        expect($type->getLabels())->toBeNull();
    });

    it('can set the labels', function () {
        $labels = ['en_US' => 'Cross sell', 'fr_FR' => 'Vente croisée'];
        $type = (new AssociationType('X_SELL'))->setLabels($labels);
        expect($type->getLabels())->toBe($labels);
    });

    it('can set the labels to null', function () {
        $labels = ['en_US' => 'Test'];
        $type = (new AssociationType('X_SELL'))->setLabels($labels)->setLabels(null);
        expect($type->getLabels())->toBeNull();
    });
});
