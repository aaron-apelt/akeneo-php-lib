<?php

declare(strict_types=1);

use AkeneoLib\Entity\ReferenceEntity;

describe('code management', function () {
    it('can get the code', function () {
        $entity = new ReferenceEntity('brand');
        expect($entity->getCode())->toBe('brand');
    });

    it('can set the code', function () {
        $entity = (new ReferenceEntity('old_code'))->setCode('new_code');
        expect($entity->getCode())->toBe('new_code');
    });
});

describe('labels management', function () {
    it('can get the labels (initially null)', function () {
        $entity = new ReferenceEntity('brand');
        expect($entity->getLabels())->toBeNull();
    });

    it('can set the labels', function () {
        $labels = ['en_US' => 'Brand', 'fr_FR' => 'Marque'];
        $entity = (new ReferenceEntity('brand'))->setLabels($labels);
        expect($entity->getLabels())->toBe($labels);
    });

    it('can set the labels to null', function () {
        $labels = ['en_US' => 'Test'];
        $entity = (new ReferenceEntity('brand'))->setLabels($labels)->setLabels(null);
        expect($entity->getLabels())->toBeNull();
    });
});

describe('image management', function () {
    it('can get the image (initially null)', function () {
        $entity = new ReferenceEntity('brand');
        expect($entity->getImage())->toBeNull();
    });

    it('can set the image', function () {
        $entity = (new ReferenceEntity('brand'))->setImage('brand-logo.png');
        expect($entity->getImage())->toBe('brand-logo.png');
    });

    it('can set the image to null', function () {
        $entity = (new ReferenceEntity('brand'))->setImage('logo.png')->setImage(null);
        expect($entity->getImage())->toBeNull();
    });
});
