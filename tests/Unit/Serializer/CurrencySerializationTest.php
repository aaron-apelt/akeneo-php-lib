<?php

declare(strict_types=1);

use AkeneoLib\Entity\Currency;
use AkeneoLib\Serializer\Serializer;

beforeEach(function () {
    $this->serializer = new Serializer;
});

describe('Currency deserialization from Akeneo API responses', function () {
    it('deserializes an enabled currency', function () {
        $apiResponse = [
            'code' => 'USD',
            'enabled' => true,
            'label' => 'US Dollar',
        ];

        $currency = $this->serializer->denormalize($apiResponse, Currency::class);

        expect($currency)->toBeInstanceOf(Currency::class)
            ->and($currency->getCode())->toBe('USD')
            ->and($currency->isEnabled())->toBeTrue()
            ->and($currency->getLabel())->toBe('US Dollar');
    });

    it('deserializes a disabled currency', function () {
        $apiResponse = [
            'code' => 'JPY',
            'enabled' => false,
            'label' => 'Japanese Yen',
        ];

        $currency = $this->serializer->denormalize($apiResponse, Currency::class);

        expect($currency->getCode())->toBe('JPY')
            ->and($currency->isEnabled())->toBeFalse()
            ->and($currency->getLabel())->toBe('Japanese Yen');
    });

    it('deserializes multiple currencies', function () {
        $currencies = [
            ['code' => 'EUR', 'enabled' => true, 'label' => 'Euro'],
            ['code' => 'GBP', 'enabled' => true, 'label' => 'British Pound'],
            ['code' => 'CHF', 'enabled' => false, 'label' => 'Swiss Franc'],
        ];

        $deserialized = array_map(
            fn ($data) => $this->serializer->denormalize($data, Currency::class),
            $currencies
        );

        expect($deserialized)->toHaveCount(3)
            ->and($deserialized[0]->getCode())->toBe('EUR')
            ->and($deserialized[2]->isEnabled())->toBeFalse();
    });
});

describe('Currency normalization to Akeneo API format', function () {
    it('normalizes a currency to API format', function () {
        $currency = new Currency('CAD');
        $currency->setEnabled(true)
            ->setLabel('Canadian Dollar');

        $normalized = $this->serializer->normalize($currency);

        expect($normalized)->toBeArray()
            ->and($normalized['code'])->toBe('CAD')
            ->and($normalized['enabled'])->toBeTrue()
            ->and($normalized['label'])->toBe('Canadian Dollar');
    });
});

describe('Currency round-trip serialization', function () {
    it('maintains data integrity through serialize-deserialize cycle', function () {
        $originalData = [
            'code' => 'AUD',
            'enabled' => true,
            'label' => 'Australian Dollar',
        ];

        $currency = $this->serializer->denormalize($originalData, Currency::class);
        $normalized = $this->serializer->normalize($currency);

        expect($normalized['code'])->toBe($originalData['code'])
            ->and($normalized['enabled'])->toBe($originalData['enabled'])
            ->and($normalized['label'])->toBe($originalData['label']);
    });
});
