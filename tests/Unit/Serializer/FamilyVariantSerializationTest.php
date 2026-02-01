<?php

declare(strict_types=1);

use AkeneoLib\Entity\FamilyVariant;
use AkeneoLib\Serializer\Serializer;

beforeEach(function () {
    $this->serializer = new Serializer;
});

describe('FamilyVariant deserialization from Akeneo API responses', function () {
    it('deserializes a complete family variant', function () {
        $apiResponse = [
            'code' => 'clothing_color_size',
            'family' => 'clothing',
            'labels' => [
                'en_US' => 'Clothing by color and size',
                'fr_FR' => 'Vêtements par couleur et taille',
            ],
            'variant_attribute_sets' => [
                [
                    'level' => 1,
                    'axes' => ['color'],
                    'attributes' => ['color', 'composition', 'material'],
                ],
                [
                    'level' => 2,
                    'axes' => ['size'],
                    'attributes' => ['size', 'ean'],
                ],
            ],
        ];

        $familyVariant = $this->serializer->denormalize($apiResponse, FamilyVariant::class);

        expect($familyVariant)->toBeInstanceOf(FamilyVariant::class)
            ->and($familyVariant->getCode())->toBe('clothing_color_size')
            ->and($familyVariant->getFamily())->toBe('clothing')
            ->and($familyVariant->getLabels())->toHaveKey('en_US')
            ->and($familyVariant->getVariantAttributeSets())->toHaveCount(2)
            ->and($familyVariant->getVariantAttributeSets()[0]['level'])->toBe(1)
            ->and($familyVariant->getVariantAttributeSets()[1]['axes'])->toContain('size');
    });

    it('deserializes a single-level family variant', function () {
        $apiResponse = [
            'code' => 'simple_variant',
            'family' => 'accessories',
            'labels' => ['en_US' => 'Accessories by color'],
            'variant_attribute_sets' => [
                [
                    'level' => 1,
                    'axes' => ['color'],
                    'attributes' => ['color', 'sku'],
                ],
            ],
        ];

        $familyVariant = $this->serializer->denormalize($apiResponse, FamilyVariant::class);

        expect($familyVariant->getVariantAttributeSets())->toHaveCount(1)
            ->and($familyVariant->getVariantAttributeSets()[0]['axes'])->toContain('color');
    });
});

describe('FamilyVariant normalization to Akeneo API format', function () {
    it('normalizes a family variant to API format', function () {
        $familyVariant = new FamilyVariant('shoes', 'shoes_size');
        $familyVariant->setLabels(['en_US' => 'Shoes by size'])
            ->setVariantAttributeSets([
                [
                    'level' => 1,
                    'axes' => ['size'],
                    'attributes' => ['size', 'color'],
                ],
            ]);

        $normalized = $this->serializer->normalize($familyVariant);

        expect($normalized)->toBeArray()
            ->and($normalized['code'])->toBe('shoes_size')
            ->and($normalized['family'])->toBe('shoes')
            ->and($normalized)->toHaveKey('variant_attribute_sets');
    });
});

describe('FamilyVariant round-trip serialization', function () {
    it('maintains data integrity through serialize-deserialize cycle', function () {
        $originalData = [
            'code' => 'electronics_variant',
            'family' => 'electronics',
            'labels' => ['en_US' => 'Electronics variant'],
            'variant_attribute_sets' => [
                ['level' => 1, 'axes' => ['model'], 'attributes' => ['model', 'brand']],
            ],
        ];

        $familyVariant = $this->serializer->denormalize($originalData, FamilyVariant::class);
        $normalized = $this->serializer->normalize($familyVariant);

        expect($normalized['code'])->toBe($originalData['code'])
            ->and($normalized['family'])->toBe($originalData['family'])
            ->and($normalized['variant_attribute_sets'])->toHaveCount(1);
    });
});
