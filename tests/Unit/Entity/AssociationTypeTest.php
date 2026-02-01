<?php

declare(strict_types=1);

use AkeneoLib\Entity\AssociationType;

describe('code management', function () {
    it('can get the code', function () {
        $type = new AssociationType('X_SELL');
        expect($type->getCode())->toBe('X_SELL');
    });

    it('can set the code', function () {
        $type = new AssociationType('old_code')->setCode('new_code');
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
        $type = new AssociationType('X_SELL')->setLabels($labels);
        expect($type->getLabels())->toBe($labels);
    });

    it('can set the labels to null', function () {
        $labels = ['en_US' => 'Test'];
        $type = new AssociationType('X_SELL')->setLabels($labels)->setLabels(null);
        expect($type->getLabels())->toBeNull();
    });
});

describe('is two way management', function () {
    it('can check if two way (initially null)', function () {
        $type = new AssociationType('X_SELL');
        expect($type->isTwoWay())->toBeNull();
    });

    it('can set two way to true', function () {
        $type = new AssociationType('X_SELL')->setIsTwoWay(true);
        expect($type->isTwoWay())->toBeTrue();
    });

    it('can set two way to false', function () {
        $type = new AssociationType('X_SELL')->setIsTwoWay(false);
        expect($type->isTwoWay())->toBeFalse();
    });

    it('can set two way to null', function () {
        $type = new AssociationType('X_SELL')->setIsTwoWay(true)->setIsTwoWay(null);
        expect($type->isTwoWay())->toBeNull();
    });
});

describe('is quantified management', function () {
    it('can check if quantified (initially null)', function () {
        $type = new AssociationType('PRODUCT_SET');
        expect($type->isQuantified())->toBeNull();
    });

    it('can set quantified to true', function () {
        $type = new AssociationType('PRODUCT_SET')->setIsQuantified(true);
        expect($type->isQuantified())->toBeTrue();
    });

    it('can set quantified to false', function () {
        $type = new AssociationType('X_SELL')->setIsQuantified(false);
        expect($type->isQuantified())->toBeFalse();
    });

    it('can set quantified to null', function () {
        $type = new AssociationType('PRODUCT_SET')->setIsQuantified(true)->setIsQuantified(null);
        expect($type->isQuantified())->toBeNull();
    });
});
