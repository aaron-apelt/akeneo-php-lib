<?php

declare(strict_types=1);

use AkeneoLib\Entity\Asset;
use AkeneoLib\Serializer\Serializer;

beforeEach(function () {
    $this->serializer = new Serializer;
});

describe('Asset deserialization from Akeneo API responses', function () {
    it('deserializes a complete asset', function () {
        $apiResponse = [
            'code' => 'packshot_product_123',
            'values' => [
                'media_preview' => [
                    [
                        'locale' => null,
                        'channel' => null,
                        'data' => '5/1/d/8/51d81dc778ba1501a8f998f3ab5797569f3b9e25_packshot.jpg',
                    ],
                ],
                'alt_tag' => [
                    [
                        'locale' => 'en_US',
                        'channel' => null,
                        'data' => 'Product packshot',
                    ],
                    [
                        'locale' => 'fr_FR',
                        'channel' => null,
                        'data' => 'Photo produit',
                    ],
                ],
            ],
        ];

        $asset = $this->serializer->denormalize($apiResponse, Asset::class);

        expect($asset)->toBeInstanceOf(Asset::class)
            ->and($asset->getCode())->toBe('packshot_product_123')
            ->and($asset->getValues())->toBeInstanceOf(AkeneoLib\Entity\Values::class);
    });

    it('deserializes an asset with asset family code', function () {
        $apiResponse = [
            'code' => 'image_001',
            'asset_family_code' => 'product_images',
            'values' => [
                'label' => [
                    ['locale' => 'en_US', 'channel' => null, 'data' => 'Image 001'],
                ],
            ],
        ];

        $asset = $this->serializer->denormalize($apiResponse, Asset::class);

        expect($asset->getAssetFamilyCode())->toBe('product_images');
    });
});

describe('Asset normalization to Akeneo API format', function () {
    it('normalizes an asset to API format', function () {
        $asset = new Asset('technical_doc_001');
        $asset->setAssetFamilyCode('technical_documentation');

        $normalized = $this->serializer->normalize($asset);

        expect($normalized)->toBeArray()
            ->and($normalized['code'])->toBe('technical_doc_001')
            ->and($normalized)->toHaveKey('asset_family_code')
            ->and($normalized['asset_family_code'])->toBe('technical_documentation');
    });
});

describe('Asset round-trip serialization', function () {
    it('maintains data integrity through serialize-deserialize cycle', function () {
        $originalData = [
            'code' => 'video_001',
            'asset_family_code' => 'product_videos',
        ];

        $asset = $this->serializer->denormalize($originalData, Asset::class);
        $normalized = $this->serializer->normalize($asset);

        expect($normalized['code'])->toBe($originalData['code'])
            ->and($normalized['asset_family_code'])->toBe($originalData['asset_family_code']);
    });
});
