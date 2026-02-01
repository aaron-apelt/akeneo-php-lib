<?php

declare(strict_types=1);

use AkeneoLib\Entity\ReferenceEntityRecord;
use AkeneoLib\Serializer\Serializer;

beforeEach(function () {
    $this->serializer = new Serializer;
});

describe('ReferenceEntityRecord deserialization from Akeneo API responses', function () {
    it('deserializes a complete reference entity record', function () {
        $apiResponse = [
            'code' => 'kartell',
            'values' => [
                'label' => [
                    [
                        'locale' => 'en_US',
                        'channel' => null,
                        'data' => 'Kartell',
                    ],
                    [
                        'locale' => 'fr_FR',
                        'channel' => null,
                        'data' => 'Kartell',
                    ],
                ],
                'description' => [
                    [
                        'locale' => 'en_US',
                        'channel' => null,
                        'data' => 'Italian furniture brand',
                    ],
                ],
                'country' => [
                    [
                        'locale' => null,
                        'channel' => null,
                        'data' => 'italy',
                    ],
                ],
            ],
            'created' => '2023-01-15T10:30:00+00:00',
            'updated' => '2023-06-20T14:45:00+00:00',
        ];

        $record = $this->serializer->denormalize($apiResponse, ReferenceEntityRecord::class);

        expect($record)->toBeInstanceOf(ReferenceEntityRecord::class)
            ->and($record->getCode())->toBe('kartell')
            ->and($record->getValues())->toBeInstanceOf(AkeneoLib\Entity\Values::class)
            ->and($record->getCreated())->toBe('2023-01-15T10:30:00+00:00')
            ->and($record->getUpdated())->toBe('2023-06-20T14:45:00+00:00');
    });

    it('deserializes a reference entity record with multi-locale values', function () {
        $apiResponse = [
            'code' => 'acme_brand',
            'values' => [
                'name' => [
                    ['locale' => 'en_US', 'channel' => null, 'data' => 'ACME Corporation'],
                    ['locale' => 'fr_FR', 'channel' => null, 'data' => 'ACME Corporation'],
                    ['locale' => 'de_DE', 'channel' => null, 'data' => 'ACME Konzern'],
                ],
                'founded' => [
                    ['locale' => null, 'channel' => null, 'data' => '1950'],
                ],
            ],
            'created' => '2023-01-01T00:00:00+00:00',
            'updated' => '2023-12-31T23:59:59+00:00',
        ];

        $record = $this->serializer->denormalize($apiResponse, ReferenceEntityRecord::class);

        expect($record->getValues())->toBeInstanceOf(AkeneoLib\Entity\Values::class);
    });
});

describe('ReferenceEntityRecord normalization to Akeneo API format', function () {
    it('normalizes a reference entity record to API format', function () {
        $record = new ReferenceEntityRecord('vitra');
        $record->setCreated('2023-01-01T00:00:00+00:00')
            ->setUpdated('2023-06-01T12:00:00+00:00');

        $normalized = $this->serializer->normalize($record);

        expect($normalized)->toBeArray()
            ->and($normalized['code'])->toBe('vitra')
            ->and($normalized['created'])->toBe('2023-01-01T00:00:00+00:00')
            ->and($normalized['updated'])->toBe('2023-06-01T12:00:00+00:00');
    });
});

describe('ReferenceEntityRecord round-trip serialization', function () {
    it('maintains data integrity through serialize-deserialize cycle', function () {
        $originalData = [
            'code' => 'muuto',
            'created' => '2023-02-01T00:00:00+00:00',
            'updated' => '2023-07-01T00:00:00+00:00',
        ];

        $record = $this->serializer->denormalize($originalData, ReferenceEntityRecord::class);
        $normalized = $this->serializer->normalize($record);

        expect($normalized['code'])->toBe($originalData['code'])
            ->and($normalized['created'])->toBe($originalData['created'])
            ->and($normalized['updated'])->toBe($originalData['updated']);
    });
});
