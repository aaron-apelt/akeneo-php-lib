<?php

declare(strict_types=1);

use AkeneoLib\Entity\Locale;
use AkeneoLib\Serializer\Serializer;

beforeEach(function () {
    $this->serializer = new Serializer;
});

describe('Locale deserialization from Akeneo API responses', function () {
    it('deserializes an enabled locale', function () {
        $apiResponse = [
            'code' => 'en_US',
            'enabled' => true,
        ];

        $locale = $this->serializer->denormalize($apiResponse, Locale::class);

        expect($locale)->toBeInstanceOf(Locale::class)
            ->and($locale->getCode())->toBe('en_US')
            ->and($locale->isEnabled())->toBeTrue();
    });

    it('deserializes a disabled locale', function () {
        $apiResponse = [
            'code' => 'zh_CN',
            'enabled' => false,
        ];

        $locale = $this->serializer->denormalize($apiResponse, Locale::class);

        expect($locale->getCode())->toBe('zh_CN')
            ->and($locale->isEnabled())->toBeFalse();
    });

    it('deserializes multiple locales', function () {
        $locales = [
            ['code' => 'en_US', 'enabled' => true],
            ['code' => 'fr_FR', 'enabled' => true],
            ['code' => 'de_DE', 'enabled' => false],
        ];

        $deserialized = array_map(
            fn ($data) => $this->serializer->denormalize($data, Locale::class),
            $locales
        );

        expect($deserialized)->toHaveCount(3)
            ->and($deserialized[0]->isEnabled())->toBeTrue()
            ->and($deserialized[2]->isEnabled())->toBeFalse();
    });
});

describe('Locale normalization to Akeneo API format', function () {
    it('normalizes a locale to API format', function () {
        $locale = new Locale('pt_BR');
        $locale->setEnabled(true);

        $normalized = $this->serializer->normalize($locale);

        expect($normalized)->toBeArray()
            ->and($normalized['code'])->toBe('pt_BR')
            ->and($normalized['enabled'])->toBeTrue();
    });
});

describe('Locale round-trip serialization', function () {
    it('maintains data integrity through serialize-deserialize cycle', function () {
        $originalData = [
            'code' => 'ja_JP',
            'enabled' => true,
        ];

        $locale = $this->serializer->denormalize($originalData, Locale::class);
        $normalized = $this->serializer->normalize($locale);

        expect($normalized['code'])->toBe($originalData['code'])
            ->and($normalized['enabled'])->toBe($originalData['enabled']);
    });
});
