<?php

declare(strict_types=1);

use AkeneoLib\Entity\Attribute;
use AkeneoLib\Serializer\Serializer;

beforeEach(function () {
    $this->serializer = new Serializer;
});

describe('Attribute deserialization from Akeneo API responses', function () {
    it('deserializes a text attribute with all properties', function () {
        $apiResponse = [
            'code' => 'description',
            'type' => 'pim_catalog_textarea',
            'labels' => ['en_US' => 'Description', 'fr_FR' => 'Description'],
            'group' => 'marketing',
            'scopable' => true,
            'localizable' => true,
            'unique' => false,
            'useable_as_grid_filter' => false,
            'max_characters' => 65535,
            'wysiwyg_enabled' => true,
            'sort_order' => 10,
        ];

        $attribute = $this->serializer->denormalize($apiResponse, Attribute::class);

        expect($attribute)->toBeInstanceOf(Attribute::class)
            ->and($attribute->getCode())->toBe('description')
            ->and($attribute->getType())->toBe('pim_catalog_textarea')
            ->and($attribute->isScopable())->toBeTrue()
            ->and($attribute->isLocalizable())->toBeTrue()
            ->and($attribute->isUnique())->toBeFalse()
            ->and($attribute->getMaxCharacters())->toBe(65535)
            ->and($attribute->isWysiwygEnabled())->toBeTrue();
    });

    it('deserializes a number attribute', function () {
        $apiResponse = [
            'code' => 'weight',
            'type' => 'pim_catalog_number',
            'labels' => ['en_US' => 'Weight'],
            'group' => 'technical',
            'scopable' => false,
            'localizable' => false,
            'decimals_allowed' => true,
            'negative_allowed' => false,
            'number_min' => 0,
            'number_max' => 10000,
        ];

        $attribute = $this->serializer->denormalize($apiResponse, Attribute::class);

        expect($attribute->getType())->toBe('pim_catalog_number')
            ->and($attribute->isDecimalsAllowed())->toBeTrue()
            ->and($attribute->isNegativeAllowed())->toBeFalse()
            ->and($attribute->getNumberMin())->toBe(0)
            ->and($attribute->getNumberMax())->toBe(10000);
    });

    it('deserializes a metric attribute', function () {
        $apiResponse = [
            'code' => 'length',
            'type' => 'pim_catalog_metric',
            'labels' => ['en_US' => 'Length'],
            'group' => 'technical',
            'scopable' => false,
            'localizable' => false,
            'metric_family' => 'Length',
            'default_metric_unit' => 'CENTIMETER',
            'decimals_allowed' => true,
            'negative_allowed' => false,
        ];

        $attribute = $this->serializer->denormalize($apiResponse, Attribute::class);

        expect($attribute->getMetricFamily())->toBe('Length')
            ->and($attribute->getDefaultMetricUnit())->toBe('CENTIMETER');
    });

    it('deserializes a file attribute', function () {
        $apiResponse = [
            'code' => 'product_manual',
            'type' => 'pim_catalog_file',
            'labels' => ['en_US' => 'Product Manual'],
            'group' => 'media',
            'scopable' => false,
            'localizable' => false,
            'max_file_size' => '10',
            'allowed_extensions' => ['pdf', 'doc', 'docx'],
        ];

        $attribute = $this->serializer->denormalize($apiResponse, Attribute::class);

        expect($attribute->getMaxFileSize())->toBe('10')
            ->and($attribute->getAllowedExtensions())->toContain('pdf');
    });

    it('deserializes an identifier attribute', function () {
        $apiResponse = [
            'code' => 'sku',
            'type' => 'pim_catalog_identifier',
            'labels' => ['en_US' => 'SKU'],
            'group' => 'general',
            'scopable' => false,
            'localizable' => false,
            'unique' => true,
            'is_main_identifier' => true,
        ];

        $attribute = $this->serializer->denormalize($apiResponse, Attribute::class);

        expect($attribute->isUnique())->toBeTrue()
            ->and($attribute->isMainIdentifier())->toBeTrue();
    });
});

describe('Attribute normalization to Akeneo API format', function () {
    it('normalizes an attribute to API format', function () {
        $attribute = new Attribute('color');
        $attribute->setType('pim_catalog_simpleselect')
            ->setLabels(['en_US' => 'Color'])
            ->setGroup('general')
            ->setScopable(false)
            ->setLocalizable(false)
            ->setUnique(false);

        $normalized = $this->serializer->normalize($attribute);

        expect($normalized)->toBeArray()
            ->and($normalized['code'])->toBe('color')
            ->and($normalized['type'])->toBe('pim_catalog_simpleselect')
            ->and($normalized['scopable'])->toBeFalse()
            ->and($normalized['localizable'])->toBeFalse();
    });
});

describe('Attribute round-trip serialization', function () {
    it('maintains data integrity through serialize-deserialize cycle', function () {
        $originalData = [
            'code' => 'price',
            'type' => 'pim_catalog_price_collection',
            'labels' => ['en_US' => 'Price'],
            'group' => 'pricing',
            'scopable' => false,
            'localizable' => false,
            'decimals_allowed' => true,
        ];

        $attribute = $this->serializer->denormalize($originalData, Attribute::class);
        $normalized = $this->serializer->normalize($attribute);

        expect($normalized['code'])->toBe($originalData['code'])
            ->and($normalized['type'])->toBe($originalData['type'])
            ->and($normalized['decimals_allowed'])->toBe($originalData['decimals_allowed']);
    });
});
