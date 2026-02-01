<?php

declare(strict_types=1);

use AkeneoLib\Entity\Channel;
use AkeneoLib\Serializer\Serializer;

beforeEach(function () {
    $this->serializer = new Serializer;
});

describe('Channel deserialization from Akeneo API responses', function () {
    it('deserializes a complete channel with all properties', function () {
        $apiResponse = [
            'code' => 'ecommerce',
            'labels' => [
                'en_US' => 'E-commerce',
                'fr_FR' => 'E-commerce',
                'de_DE' => 'E-Commerce',
            ],
            'currencies' => ['USD', 'EUR', 'GBP'],
            'locales' => ['en_US', 'fr_FR', 'de_DE'],
            'category_tree' => 'master',
            'conversion_units' => [
                'weight' => 'KILOGRAM',
                'length' => 'METER',
            ],
        ];

        $channel = $this->serializer->denormalize($apiResponse, Channel::class);

        expect($channel)->toBeInstanceOf(Channel::class)
            ->and($channel->getCode())->toBe('ecommerce')
            ->and($channel->getLabels())->toBe(['en_US' => 'E-commerce', 'fr_FR' => 'E-commerce', 'de_DE' => 'E-Commerce'])
            ->and($channel->getCurrencies())->toBe(['USD', 'EUR', 'GBP'])
            ->and($channel->getLocales())->toBe(['en_US', 'fr_FR', 'de_DE'])
            ->and($channel->getCategoryTree())->toBe('master')
            ->and($channel->getConversionUnits())->toHaveKey('weight');
    });

    it('deserializes a minimal channel', function () {
        $apiResponse = [
            'code' => 'mobile',
            'labels' => ['en_US' => 'Mobile App'],
            'currencies' => ['USD'],
            'locales' => ['en_US'],
            'category_tree' => 'mobile_catalog',
        ];

        $channel = $this->serializer->denormalize($apiResponse, Channel::class);

        expect($channel)->toBeInstanceOf(Channel::class)
            ->and($channel->getCode())->toBe('mobile')
            ->and($channel->getCurrencies())->toHaveCount(1)
            ->and($channel->getLocales())->toHaveCount(1);
    });
});

describe('Channel normalization to Akeneo API format', function () {
    it('normalizes a complete channel to API format', function () {
        $channel = new Channel('print');
        $channel->setLabels(['en_US' => 'Print Catalog'])
            ->setCurrencies(['USD', 'EUR'])
            ->setLocales(['en_US', 'fr_FR'])
            ->setCategoryTree('print_catalog')
            ->setConversionUnits(['weight' => 'GRAM']);

        $normalized = $this->serializer->normalize($channel);

        expect($normalized)->toBeArray()
            ->and($normalized['code'])->toBe('print')
            ->and($normalized)->toHaveKey('category_tree')
            ->and($normalized['category_tree'])->toBe('print_catalog');
    });
});

describe('Channel round-trip serialization', function () {
    it('maintains data integrity through serialize-deserialize cycle', function () {
        $originalData = [
            'code' => 'tablet',
            'labels' => ['en_US' => 'Tablet Channel'],
            'currencies' => ['USD'],
            'locales' => ['en_US'],
            'category_tree' => 'master',
        ];

        $channel = $this->serializer->denormalize($originalData, Channel::class);
        $normalized = $this->serializer->normalize($channel);

        expect($normalized['code'])->toBe($originalData['code'])
            ->and($normalized['currencies'])->toBe($originalData['currencies'])
            ->and($normalized['category_tree'])->toBe($originalData['category_tree']);
    });
});
