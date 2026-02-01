<?php

declare(strict_types=1);

use AkeneoLib\Entity\ReferenceEntity;
use AkeneoLib\Serializer\Serializer;

beforeEach(function () {
    $this->serializer = new Serializer;
});

describe('ReferenceEntity deserialization from Akeneo API responses', function () {
    it('deserializes a complete reference entity', function () {
        $apiResponse = [
            'code' => 'brand',
            'labels' => [
                'en_US' => 'Brand',
                'fr_FR' => 'Marque',
                'de_DE' => 'Marke',
            ],
            'image' => '5/1/d/8/51d81dc778ba1501a8f998f3ab5797569f3b9e25_brand_logo.png',
        ];

        $referenceEntity = $this->serializer->denormalize($apiResponse, ReferenceEntity::class);

        expect($referenceEntity)->toBeInstanceOf(ReferenceEntity::class)
            ->and($referenceEntity->getCode())->toBe('brand')
            ->and($referenceEntity->getLabels())->toBe(['en_US' => 'Brand', 'fr_FR' => 'Marque', 'de_DE' => 'Marke'])
            ->and($referenceEntity->getImage())->toBe('5/1/d/8/51d81dc778ba1501a8f998f3ab5797569f3b9e25_brand_logo.png');
    });

    it('deserializes a reference entity without image', function () {
        $apiResponse = [
            'code' => 'designer',
            'labels' => ['en_US' => 'Designer'],
            'image' => null,
        ];

        $referenceEntity = $this->serializer->denormalize($apiResponse, ReferenceEntity::class);

        expect($referenceEntity->getImage())->toBeNull();
    });

    it('deserializes multiple reference entities', function () {
        $entities = [
            ['code' => 'color', 'labels' => ['en_US' => 'Color']],
            ['code' => 'material', 'labels' => ['en_US' => 'Material']],
        ];

        $deserialized = array_map(
            fn ($data) => $this->serializer->denormalize($data, ReferenceEntity::class),
            $entities
        );

        expect($deserialized)->toHaveCount(2)
            ->and($deserialized[0]->getCode())->toBe('color')
            ->and($deserialized[1]->getCode())->toBe('material');
    });
});

describe('ReferenceEntity normalization to Akeneo API format', function () {
    it('normalizes a reference entity to API format', function () {
        $referenceEntity = new ReferenceEntity('designer');
        $referenceEntity->setLabels(['en_US' => 'Designer', 'fr_FR' => 'Créateur'])
            ->setImage('designer_logo.png');

        $normalized = $this->serializer->normalize($referenceEntity);

        expect($normalized)->toBeArray()
            ->and($normalized['code'])->toBe('designer')
            ->and($normalized['image'])->toBe('designer_logo.png');
    });
});

describe('ReferenceEntity round-trip serialization', function () {
    it('maintains data integrity through serialize-deserialize cycle', function () {
        $originalData = [
            'code' => 'collection',
            'labels' => ['en_US' => 'Collection'],
            'image' => 'collection_image.jpg',
        ];

        $referenceEntity = $this->serializer->denormalize($originalData, ReferenceEntity::class);
        $normalized = $this->serializer->normalize($referenceEntity);

        expect($normalized['code'])->toBe($originalData['code'])
            ->and($normalized['image'])->toBe($originalData['image']);
    });
});
