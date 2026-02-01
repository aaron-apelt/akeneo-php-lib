<?php

declare(strict_types=1);

use AkeneoLib\Entity\AttributeOption;
use AkeneoLib\Serializer\Serializer;

beforeEach(function () {
    $this->serializer = new Serializer;
});

describe('AttributeOption deserialization from Akeneo API responses', function () {
    it('deserializes a complete attribute option', function () {
        $apiResponse = [
            'code' => 'blue',
            'labels' => [
                'en_US' => 'Blue',
                'fr_FR' => 'Bleu',
                'de_DE' => 'Blau',
            ],
            'attribute' => 'color',
            'sort_order' => 10,
        ];

        $option = $this->serializer->denormalize($apiResponse, AttributeOption::class);

        expect($option)->toBeInstanceOf(AttributeOption::class)
            ->and($option->getCode())->toBe('blue')
            ->and($option->getLabels())->toBe(['en_US' => 'Blue', 'fr_FR' => 'Bleu', 'de_DE' => 'Blau'])
            ->and($option->getAttribute())->toBe('color')
            ->and($option->getSortOrder())->toBe(10);
    });

    it('deserializes attribute options for different attributes', function () {
        $colorOption = [
            'code' => 'red',
            'labels' => ['en_US' => 'Red'],
            'attribute' => 'color',
            'sort_order' => 1,
        ];

        $sizeOption = [
            'code' => 'large',
            'labels' => ['en_US' => 'Large', 'fr_FR' => 'Grande'],
            'attribute' => 'size',
            'sort_order' => 3,
        ];

        $color = $this->serializer->denormalize($colorOption, AttributeOption::class);
        $size = $this->serializer->denormalize($sizeOption, AttributeOption::class);

        expect($color->getAttribute())->toBe('color')
            ->and($size->getAttribute())->toBe('size')
            ->and($color->getSortOrder())->toBe(1)
            ->and($size->getSortOrder())->toBe(3);
    });
});

describe('AttributeOption normalization to Akeneo API format', function () {
    it('normalizes an attribute option to API format', function () {
        $option = new AttributeOption('medium');
        $option->setLabels(['en_US' => 'Medium', 'fr_FR' => 'Moyen'])
            ->setAttribute('size')
            ->setSortOrder(2);

        $normalized = $this->serializer->normalize($option);

        expect($normalized)->toBeArray()
            ->and($normalized['code'])->toBe('medium')
            ->and($normalized)->toHaveKey('sort_order')
            ->and($normalized['sort_order'])->toBe(2);
    });
});

describe('AttributeOption round-trip serialization', function () {
    it('maintains data integrity through serialize-deserialize cycle', function () {
        $originalData = [
            'code' => 'green',
            'labels' => ['en_US' => 'Green', 'de_DE' => 'Grün'],
            'attribute' => 'color',
            'sort_order' => 5,
        ];

        $option = $this->serializer->denormalize($originalData, AttributeOption::class);
        $normalized = $this->serializer->normalize($option);

        expect($normalized['code'])->toBe($originalData['code'])
            ->and($normalized['attribute'])->toBe($originalData['attribute'])
            ->and($normalized['sort_order'])->toBe($originalData['sort_order']);
    });
});
