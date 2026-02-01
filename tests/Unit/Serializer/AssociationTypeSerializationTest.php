<?php

declare(strict_types=1);

use AkeneoLib\Entity\AssociationType;
use AkeneoLib\Serializer\Serializer;

beforeEach(function () {
    $this->serializer = new Serializer;
});

describe('AssociationType deserialization from Akeneo API responses', function () {
    it('deserializes a complete association type', function () {
        $apiResponse = [
            'code' => 'X_SELL',
            'labels' => [
                'en_US' => 'Cross sell',
                'fr_FR' => 'Vente croisée',
                'de_DE' => 'Cross-Selling',
            ],
            'is_two_way' => false,
            'is_quantified' => false,
        ];

        $associationType = $this->serializer->denormalize($apiResponse, AssociationType::class);

        expect($associationType)->toBeInstanceOf(AssociationType::class)
            ->and($associationType->getCode())->toBe('X_SELL')
            ->and($associationType->getLabels())->toBe(['en_US' => 'Cross sell', 'fr_FR' => 'Vente croisée', 'de_DE' => 'Cross-Selling'])
            ->and($associationType->isTwoWay())->toBeFalse()
            ->and($associationType->isQuantified())->toBeFalse();
    });

    it('deserializes a two-way association type', function () {
        $apiResponse = [
            'code' => 'SUBSTITUTION',
            'labels' => ['en_US' => 'Substitution'],
            'is_two_way' => true,
            'is_quantified' => false,
        ];

        $associationType = $this->serializer->denormalize($apiResponse, AssociationType::class);

        expect($associationType->isTwoWay())->toBeTrue();
    });

    it('deserializes a quantified association type', function () {
        $apiResponse = [
            'code' => 'PACK',
            'labels' => ['en_US' => 'Pack'],
            'is_two_way' => false,
            'is_quantified' => true,
        ];

        $associationType = $this->serializer->denormalize($apiResponse, AssociationType::class);

        expect($associationType->isQuantified())->toBeTrue();
    });
});

describe('AssociationType normalization to Akeneo API format', function () {
    it('normalizes an association type to API format', function () {
        $associationType = new AssociationType('UPSELL');
        $associationType->setLabels(['en_US' => 'Upsell', 'fr_FR' => 'Vente incitative'])
            ->setIsTwoWay(false)
            ->setIsQuantified(false);

        $normalized = $this->serializer->normalize($associationType);

        expect($normalized)->toBeArray()
            ->and($normalized['code'])->toBe('UPSELL')
            ->and($normalized)->toHaveKey('is_two_way')
            ->and($normalized['is_two_way'])->toBeFalse();
    });
});

describe('AssociationType round-trip serialization', function () {
    it('maintains data integrity through serialize-deserialize cycle', function () {
        $originalData = [
            'code' => 'RELATED',
            'labels' => ['en_US' => 'Related Products'],
            'is_two_way' => true,
            'is_quantified' => false,
        ];

        $associationType = $this->serializer->denormalize($originalData, AssociationType::class);
        $normalized = $this->serializer->normalize($associationType);

        expect($normalized['code'])->toBe($originalData['code'])
            ->and($normalized['is_two_way'])->toBe($originalData['is_two_way'])
            ->and($normalized['is_quantified'])->toBe($originalData['is_quantified']);
    });
});
