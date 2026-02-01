<?php

declare(strict_types=1);

use AkeneoLib\Entity\Category;
use AkeneoLib\Serializer\Serializer;

beforeEach(function () {
    $this->serializer = new Serializer;
});

describe('Category deserialization from Akeneo API responses', function () {
    it('deserializes a complete category with all properties', function () {
        $apiResponse = [
            'code' => 't-shirts',
            'parent' => 'clothing',
            'labels' => [
                'en_US' => 'T-Shirts',
                'fr_FR' => 'T-Shirts',
                'de_DE' => 'T-Shirts',
            ],
            'position' => 5,
            'channel_requirements' => [
                'ecommerce' => ['en_US', 'fr_FR'],
                'print' => ['en_US'],
            ],
        ];

        $category = $this->serializer->denormalize($apiResponse, Category::class);

        expect($category)->toBeInstanceOf(Category::class)
            ->and($category->getCode())->toBe('t-shirts')
            ->and($category->getParent())->toBe('clothing')
            ->and($category->getLabels())->toBe(['en_US' => 'T-Shirts', 'fr_FR' => 'T-Shirts', 'de_DE' => 'T-Shirts'])
            ->and($category->getPosition())->toBe(5)
            ->and($category->getChannelRequirements())->toHaveKey('ecommerce');
    });

    it('deserializes a root category without parent', function () {
        $apiResponse = [
            'code' => 'master',
            'parent' => null,
            'labels' => ['en_US' => 'Master Catalog'],
        ];

        $category = $this->serializer->denormalize($apiResponse, Category::class);

        expect($category->getCode())->toBe('master')
            ->and($category->getParent())->toBeNull();
    });

    it('deserializes hierarchical categories', function () {
        $rootData = ['code' => 'catalog', 'parent' => null, 'labels' => ['en_US' => 'Catalog']];
        $level1Data = ['code' => 'clothing', 'parent' => 'catalog', 'labels' => ['en_US' => 'Clothing']];
        $level2Data = ['code' => 'shirts', 'parent' => 'clothing', 'labels' => ['en_US' => 'Shirts']];

        $root = $this->serializer->denormalize($rootData, Category::class);
        $level1 = $this->serializer->denormalize($level1Data, Category::class);
        $level2 = $this->serializer->denormalize($level2Data, Category::class);

        expect($root->getParent())->toBeNull()
            ->and($level1->getParent())->toBe('catalog')
            ->and($level2->getParent())->toBe('clothing');
    });
});

describe('Category normalization to Akeneo API format', function () {
    it('normalizes a complete category to API format', function () {
        $category = new Category('shoes', 'footwear', ['en_US' => 'Shoes']);
        $category->setPosition(10)
            ->setChannelRequirements([
                'ecommerce' => ['en_US', 'fr_FR'],
            ]);

        $normalized = $this->serializer->normalize($category);

        expect($normalized)->toBeArray()
            ->and($normalized['code'])->toBe('shoes')
            ->and($normalized['parent'])->toBe('footwear')
            ->and($normalized['labels'])->toBe(['en_US' => 'Shoes'])
            ->and($normalized['position'])->toBe(10);
    });
});

describe('Category round-trip serialization', function () {
    it('maintains data integrity through serialize-deserialize cycle', function () {
        $originalData = [
            'code' => 'accessories',
            'parent' => 'catalog',
            'labels' => ['en_US' => 'Accessories', 'fr_FR' => 'Accessoires'],
            'position' => 3,
        ];

        $category = $this->serializer->denormalize($originalData, Category::class);
        $normalized = $this->serializer->normalize($category);

        expect($normalized['code'])->toBe($originalData['code'])
            ->and($normalized['parent'])->toBe($originalData['parent'])
            ->and($normalized['position'])->toBe($originalData['position']);
    });
});
