<?php

declare(strict_types=1);

use AkeneoLib\Entity\AttributeGroup;
use AkeneoLib\Serializer\Serializer;

beforeEach(function () {
    $this->serializer = new Serializer;
});

describe('AttributeGroup deserialization from Akeneo API responses', function () {
    it('deserializes a complete attribute group', function () {
        $apiResponse = [
            'code' => 'marketing',
            'labels' => [
                'en_US' => 'Marketing',
                'fr_FR' => 'Marketing',
                'de_DE' => 'Marketing',
            ],
            'sort_order' => 2,
            'attributes' => ['name', 'description', 'short_description', 'meta_title', 'meta_description'],
        ];

        $group = $this->serializer->denormalize($apiResponse, AttributeGroup::class);

        expect($group)->toBeInstanceOf(AttributeGroup::class)
            ->and($group->getCode())->toBe('marketing')
            ->and($group->getLabels())->toBe(['en_US' => 'Marketing', 'fr_FR' => 'Marketing', 'de_DE' => 'Marketing'])
            ->and($group->getSortOrder())->toBe(2)
            ->and($group->getAttributes())->toHaveCount(5);
    });

    it('deserializes multiple attribute groups', function () {
        $groups = [
            ['code' => 'general', 'labels' => ['en_US' => 'General'], 'sort_order' => 1],
            ['code' => 'technical', 'labels' => ['en_US' => 'Technical'], 'sort_order' => 3],
            ['code' => 'media', 'labels' => ['en_US' => 'Media'], 'sort_order' => 4],
        ];

        $deserialized = array_map(
            fn ($data) => $this->serializer->denormalize($data, AttributeGroup::class),
            $groups
        );

        expect($deserialized)->toHaveCount(3)
            ->and($deserialized[0]->getSortOrder())->toBe(1)
            ->and($deserialized[1]->getCode())->toBe('technical');
    });
});

describe('AttributeGroup normalization to Akeneo API format', function () {
    it('normalizes an attribute group to API format', function () {
        $group = new AttributeGroup('pricing');
        $group->setLabels(['en_US' => 'Pricing', 'fr_FR' => 'Prix'])
            ->setSortOrder(5)
            ->setAttributes(['price', 'cost', 'msrp']);

        $normalized = $this->serializer->normalize($group);

        expect($normalized)->toBeArray()
            ->and($normalized['code'])->toBe('pricing')
            ->and($normalized)->toHaveKey('sort_order')
            ->and($normalized['sort_order'])->toBe(5)
            ->and($normalized['attributes'])->toHaveCount(3);
    });
});

describe('AttributeGroup round-trip serialization', function () {
    it('maintains data integrity through serialize-deserialize cycle', function () {
        $originalData = [
            'code' => 'dimensions',
            'labels' => ['en_US' => 'Dimensions'],
            'sort_order' => 6,
            'attributes' => ['width', 'height', 'depth', 'weight'],
        ];

        $group = $this->serializer->denormalize($originalData, AttributeGroup::class);
        $normalized = $this->serializer->normalize($group);

        expect($normalized['code'])->toBe($originalData['code'])
            ->and($normalized['sort_order'])->toBe($originalData['sort_order'])
            ->and($normalized['attributes'])->toBe($originalData['attributes']);
    });
});
