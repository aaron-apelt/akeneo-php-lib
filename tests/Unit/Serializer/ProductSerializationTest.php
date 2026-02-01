<?php

declare(strict_types=1);

use AkeneoLib\Entity\Product;
use AkeneoLib\Entity\Value;
use AkeneoLib\Entity\Values;
use AkeneoLib\Serializer\Serializer;

beforeEach(function () {
    $this->serializer = new Serializer;
});

describe('Product deserialization from Akeneo API responses', function () {
    it('deserializes a complete product with all properties', function () {
        $apiResponse = [
            'identifier' => 'fancy-tshirt-001',
            'enabled' => true,
            'family' => 'clothing',
            'categories' => ['summer_collection', 't-shirts', 'men'],
            'groups' => ['promotion', 'new_arrivals'],
            'parent' => null,
            'uuid' => '12f66136-d3b2-4f5f-9b9f-1d7e5e3c2a1b',
            'associations' => [
                'X_SELL' => [
                    'groups' => [],
                    'products' => ['accessory-123', 'accessory-456'],
                    'product_models' => [],
                ],
                'UPSELL' => [
                    'groups' => ['premium_items'],
                    'products' => ['premium-tshirt-001'],
                    'product_models' => ['premium-model-001'],
                ],
            ],
            'quantified_associations' => [
                'PACK' => [
                    'products' => [
                        [
                            'identifier' => 'item-002',
                            'quantity' => 2,
                        ],
                        [
                            'identifier' => 'item-003',
                            'quantity' => 1,
                        ],
                    ],
                    'product_models' => [],
                ],
            ],
            'created' => '2023-10-06T12:34:56+00:00',
            'updated' => '2023-11-10T08:45:32+00:00',
            'metadata' => [
                'workflow_status' => 'working_copy',
            ],
            'quality_scores' => [
                [
                    'scope' => 'ecommerce',
                    'locale' => 'en_US',
                    'data' => 'A',
                ],
            ],
            'completenesses' => [
                [
                    'scope' => 'ecommerce',
                    'locale' => 'en_US',
                    'data' => 95,
                ],
            ],
            'root_parent' => null,
            'workflow_execution_status' => null,
            'values' => [
                'name' => [
                    [
                        'locale' => 'en_US',
                        'scope' => null,
                        'data' => 'Fancy T-shirt',
                    ],
                    [
                        'locale' => 'fr_FR',
                        'scope' => null,
                        'data' => 'T-shirt élégant',
                    ],
                ],
                'description' => [
                    [
                        'locale' => 'en_US',
                        'scope' => 'ecommerce',
                        'data' => 'A 100% organic cotton T-shirt',
                    ],
                ],
            ],
        ];

        $product = $this->serializer->denormalize($apiResponse, Product::class);

        expect($product)->toBeInstanceOf(Product::class)
            ->and($product->getIdentifier())->toBe('fancy-tshirt-001')
            ->and($product->isEnabled())->toBeTrue()
            ->and($product->getFamily())->toBe('clothing')
            ->and($product->getCategories())->toBe(['summer_collection', 't-shirts', 'men'])
            ->and($product->getGroups())->toBe(['promotion', 'new_arrivals'])
            ->and($product->getUuid())->toBe('12f66136-d3b2-4f5f-9b9f-1d7e5e3c2a1b')
            ->and($product->getCreated())->toBe('2023-10-06T12:34:56+00:00')
            ->and($product->getUpdated())->toBe('2023-11-10T08:45:32+00:00')
            ->and($product->getAssociations())->toHaveKey('X_SELL')
            ->and($product->getAssociations()['X_SELL']['products'])->toHaveCount(2)
            ->and($product->getQuantifiedAssociations())->toHaveKey('PACK')
            ->and($product->getValues())->toBeInstanceOf(Values::class);
    });

    it('deserializes a minimal product with only required fields', function () {
        $apiResponse = [
            'identifier' => 'minimal-product',
            'enabled' => false,
            'family' => null,
            'categories' => [],
            'groups' => [],
            'parent' => null,
            'values' => [],
        ];

        $product = $this->serializer->denormalize($apiResponse, Product::class);

        expect($product)->toBeInstanceOf(Product::class)
            ->and($product->getIdentifier())->toBe('minimal-product')
            ->and($product->isEnabled())->toBeFalse()
            ->and($product->getFamily())->toBeNull();
    });

    it('deserializes a product variant with parent', function () {
        $apiResponse = [
            'identifier' => 'tshirt-blue-m',
            'enabled' => true,
            'family' => 'clothing',
            'categories' => ['t-shirts'],
            'groups' => [],
            'parent' => 'tshirt-model',
            'root_parent' => 'tshirt-master-model',
            'uuid' => 'abc-123-def-456',
            'values' => [
                'color' => [
                    [
                        'locale' => null,
                        'scope' => null,
                        'data' => 'blue',
                    ],
                ],
                'size' => [
                    [
                        'locale' => null,
                        'scope' => null,
                        'data' => 'M',
                    ],
                ],
            ],
        ];

        $product = $this->serializer->denormalize($apiResponse, Product::class);

        expect($product->getParent())->toBe('tshirt-model')
            ->and($product->getRootParent())->toBe('tshirt-master-model')
            ->and($product->getValue('color')->getData())->toBe('blue')
            ->and($product->getValue('size')->getData())->toBe('M');
    });

    it('deserializes multi-locale and multi-scope values correctly', function () {
        $apiResponse = [
            'identifier' => 'multi-value-product',
            'values' => [
                'name' => [
                    [
                        'locale' => 'en_US',
                        'scope' => null,
                        'data' => 'English Name',
                    ],
                    [
                        'locale' => 'fr_FR',
                        'scope' => null,
                        'data' => 'Nom Français',
                    ],
                    [
                        'locale' => 'de_DE',
                        'scope' => null,
                        'data' => 'Deutscher Name',
                    ],
                ],
                'description' => [
                    [
                        'locale' => 'en_US',
                        'scope' => 'ecommerce',
                        'data' => 'Ecommerce description',
                    ],
                    [
                        'locale' => 'en_US',
                        'scope' => 'print',
                        'data' => 'Print catalog description',
                    ],
                    [
                        'locale' => 'fr_FR',
                        'scope' => 'ecommerce',
                        'data' => 'Description e-commerce',
                    ],
                ],
            ],
        ];

        $product = $this->serializer->denormalize($apiResponse, Product::class);
        $values = iterator_to_array($product->getValues());

        expect($values)->toHaveCount(6)
            ->and($product->getValue('name', null, 'en_US')->getData())->toBe('English Name')
            ->and($product->getValue('name', null, 'fr_FR')->getData())->toBe('Nom Français')
            ->and($product->getValue('name', null, 'de_DE')->getData())->toBe('Deutscher Name')
            ->and($product->getValue('description', 'ecommerce', 'en_US')->getData())->toBe('Ecommerce description')
            ->and($product->getValue('description', 'print', 'en_US')->getData())->toBe('Print catalog description')
            ->and($product->getValue('description', 'ecommerce', 'fr_FR')->getData())->toBe('Description e-commerce');
    });

    it('deserializes complex value data types', function () {
        $apiResponse = [
            'identifier' => 'complex-values-product',
            'values' => [
                'price' => [
                    [
                        'locale' => null,
                        'scope' => 'ecommerce',
                        'data' => [
                            ['amount' => '29.99', 'currency' => 'USD'],
                            ['amount' => '25.99', 'currency' => 'EUR'],
                        ],
                    ],
                ],
                'image' => [
                    [
                        'locale' => null,
                        'scope' => null,
                        'data' => '/path/to/image.jpg',
                    ],
                ],
                'specifications' => [
                    [
                        'locale' => 'en_US',
                        'scope' => null,
                        'data' => [
                            'weight' => '200g',
                            'material' => 'cotton',
                            'care' => ['machine_wash', 'tumble_dry'],
                        ],
                    ],
                ],
            ],
        ];

        $product = $this->serializer->denormalize($apiResponse, Product::class);

        $priceData = $product->getValue('price', 'ecommerce')->getData();
        expect($priceData)->toBeArray()
            ->and($priceData)->toHaveCount(2)
            ->and($priceData[0]['amount'])->toBe('29.99');

        $specsData = $product->getValue('specifications', null, 'en_US')->getData();
        expect($specsData)->toBeArray()
            ->and($specsData['material'])->toBe('cotton');
    });
});

describe('Product normalization to Akeneo API format', function () {
    it('normalizes a complete product to API format', function () {
        $product = new Product('test-product-001');
        $product->setEnabled(true)
            ->setFamily('shoes')
            ->setCategories(['running', 'sports'])
            ->setGroups(['featured'])
            ->setUuid('uuid-123-abc')
            ->setAssociations([
                'X_SELL' => [
                    'products' => ['related-1'],
                    'groups' => [],
                    'product_models' => [],
                ],
            ])
            ->setQuantifiedAssociations([
                'PACK' => [
                    'products' => [
                        ['identifier' => 'item-1', 'quantity' => 2],
                    ],
                    'product_models' => [],
                ],
            ])
            ->setCreated('2024-01-01T00:00:00+00:00')
            ->setUpdated('2024-01-02T00:00:00+00:00')
            ->setMetadata(['custom' => 'data']);

        $values = new Values;
        $values->upsert(new Value('name', 'Running Shoe', null, 'en_US'));
        $product->setValues($values);

        $normalized = $this->serializer->normalize($product);

        expect($normalized)->toBeArray()
            ->and($normalized)->toHaveKey('identifier')
            ->and($normalized['identifier'])->toBe('test-product-001')
            ->and($normalized['enabled'])->toBeTrue()
            ->and($normalized['family'])->toBe('shoes')
            ->and($normalized['categories'])->toBe(['running', 'sports'])
            ->and($normalized['uuid'])->toBe('uuid-123-abc')
            ->and($normalized)->toHaveKey('values')
            ->and($normalized)->toHaveKey('associations')
            ->and($normalized)->toHaveKey('quantified_associations');
    });

    it('normalizes values back to Akeneo format', function () {
        $product = new Product('value-test');
        $values = new Values;
        $values->upsert(new Value('name', 'Product Name', null, 'en_US'));
        $values->upsert(new Value('name', 'Nom du Produit', null, 'fr_FR'));
        $values->upsert(new Value('description', 'Web description', 'ecommerce', 'en_US'));
        $product->setValues($values);

        $normalized = $this->serializer->normalize($product);

        expect($normalized['values'])->toBeArray()
            ->and($normalized['values'])->toHaveKey('name')
            ->and($normalized['values'])->toHaveKey('description')
            ->and($normalized['values']['name'])->toHaveCount(2)
            ->and($normalized['values']['description'])->toHaveCount(1);
    });
});

describe('Product round-trip serialization', function () {
    it('maintains data integrity through serialize-deserialize cycle', function () {
        $originalData = [
            'identifier' => 'roundtrip-test',
            'enabled' => true,
            'family' => 'accessories',
            'categories' => ['bags', 'travel'],
            'groups' => ['summer'],
            'uuid' => 'test-uuid-roundtrip',
            'associations' => [
                'X_SELL' => [
                    'products' => ['prod-1', 'prod-2'],
                    'groups' => [],
                    'product_models' => [],
                ],
            ],
            'created' => '2024-01-01T10:00:00+00:00',
            'updated' => '2024-01-15T14:30:00+00:00',
            'values' => [
                'name' => [
                    ['locale' => 'en_US', 'scope' => null, 'data' => 'Travel Bag'],
                ],
            ],
        ];

        // Deserialize
        $product = $this->serializer->denormalize($originalData, Product::class);

        // Serialize
        $normalized = $this->serializer->normalize($product);

        // Verify key properties maintained
        expect($normalized['identifier'])->toBe($originalData['identifier'])
            ->and($normalized['enabled'])->toBe($originalData['enabled'])
            ->and($normalized['family'])->toBe($originalData['family'])
            ->and($normalized['categories'])->toBe($originalData['categories'])
            ->and($normalized['uuid'])->toBe($originalData['uuid'])
            ->and($normalized['created'])->toBe($originalData['created'])
            ->and($normalized['updated'])->toBe($originalData['updated']);
    });

    it('handles null values correctly in round-trip', function () {
        $originalData = [
            'identifier' => 'null-test',
            'enabled' => true,
            'family' => null,
            'categories' => null,
            'groups' => null,
            'parent' => null,
            'root_parent' => null,
            'values' => [],
        ];

        $product = $this->serializer->denormalize($originalData, Product::class);
        $normalized = $this->serializer->normalize($product);

        expect($product->getFamily())->toBeNull()
            ->and($product->getCategories())->toBeNull()
            ->and($product->getParent())->toBeNull()
            ->and($product->getRootParent())->toBeNull();
    });
});
