<?php

declare(strict_types=1);

use AkeneoLib\Entity\Family;
use AkeneoLib\Serializer\Serializer;

beforeEach(function () {
    $this->serializer = new Serializer;
});

describe('Family deserialization from Akeneo API responses', function () {
    it('deserializes a complete family with all properties', function () {
        $apiResponse = [
            'code' => 'clothing',
            'labels' => [
                'en_US' => 'Clothing',
                'fr_FR' => 'Vêtements',
                'de_DE' => 'Kleidung',
            ],
            'attributes' => ['sku', 'name', 'description', 'price', 'color', 'size'],
            'attribute_as_label' => 'name',
            'attribute_as_image' => 'main_image',
            'attribute_as_main_media' => 'product_video',
            'attribute_requirements' => [
                'ecommerce' => ['sku', 'name', 'price'],
                'print' => ['sku', 'name', 'description'],
            ],
            'parent' => null,
        ];

        $family = $this->serializer->denormalize($apiResponse, Family::class);

        expect($family)->toBeInstanceOf(Family::class)
            ->and($family->getCode())->toBe('clothing')
            ->and($family->getLabels())->toBe(['en_US' => 'Clothing', 'fr_FR' => 'Vêtements', 'de_DE' => 'Kleidung'])
            ->and($family->getAttributes())->toHaveCount(6)
            ->and($family->getAttributeAsLabel())->toBe('name')
            ->and($family->getAttributeAsImage())->toBe('main_image')
            ->and($family->getAttributeAsMainMedia())->toBe('product_video')
            ->and($family->getAttributeRequirements())->toHaveKey('ecommerce')
            ->and($family->getParent())->toBeNull();
    });

    it('deserializes a minimal family', function () {
        $apiResponse = [
            'code' => 'accessories',
            'labels' => ['en_US' => 'Accessories'],
            'attributes' => ['sku'],
            'attribute_as_label' => 'sku',
        ];

        $family = $this->serializer->denormalize($apiResponse, Family::class);

        expect($family)->toBeInstanceOf(Family::class)
            ->and($family->getCode())->toBe('accessories')
            ->and($family->getAttributeAsLabel())->toBe('sku');
    });
});

describe('Family normalization to Akeneo API format', function () {
    it('normalizes a complete family to API format', function () {
        $family = new Family('shoes');
        $family->setLabels(['en_US' => 'Shoes', 'fr_FR' => 'Chaussures'])
            ->setAttributes(['sku', 'name', 'size'])
            ->setAttributeAsLabel('name')
            ->setAttributeAsImage('image')
            ->setAttributeRequirements([
                'ecommerce' => ['sku', 'name'],
            ]);

        $normalized = $this->serializer->normalize($family);

        expect($normalized)->toBeArray()
            ->and($normalized)->toHaveKey('code')
            ->and($normalized['code'])->toBe('shoes')
            ->and($normalized)->toHaveKey('attribute_as_label')
            ->and($normalized['attribute_as_label'])->toBe('name');
    });
});

describe('Family round-trip serialization', function () {
    it('maintains data integrity through serialize-deserialize cycle', function () {
        $originalData = [
            'code' => 'electronics',
            'labels' => ['en_US' => 'Electronics'],
            'attributes' => ['sku', 'model', 'brand'],
            'attribute_as_label' => 'model',
            'attribute_requirements' => [
                'ecommerce' => ['sku', 'model'],
            ],
        ];

        $family = $this->serializer->denormalize($originalData, Family::class);
        $normalized = $this->serializer->normalize($family);

        expect($normalized['code'])->toBe($originalData['code'])
            ->and($normalized['labels'])->toBe($originalData['labels'])
            ->and($normalized['attributes'])->toBe($originalData['attributes']);
    });
});
