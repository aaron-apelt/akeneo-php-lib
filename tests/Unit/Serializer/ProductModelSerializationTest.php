<?php

declare(strict_types=1);

use AkeneoLib\Entity\ProductModel;
use AkeneoLib\Entity\Value;
use AkeneoLib\Entity\Values;
use AkeneoLib\Serializer\Serializer;

beforeEach(function () {
    $this->serializer = new Serializer;
});

describe('ProductModel deserialization from Akeneo API responses', function () {
    it('deserializes a complete product model with all properties', function () {
        $apiResponse = [
            'code' => 'tshirt-model-master',
            'family' => 'clothing',
            'family_variant' => 'clothing_color_size',
            'parent' => null,
            'categories' => ['t-shirts', 'summer_collection', 'men'],
            'associations' => [
                'SUBSTITUTION' => [
                    'products' => [],
                    'product_models' => ['alternative-model-001', 'alternative-model-002'],
                    'groups' => [],
                ],
                'UPSELL' => [
                    'products' => ['premium-product-001'],
                    'product_models' => [],
                    'groups' => ['premium'],
                ],
            ],
            'quantified_associations' => [
                'PACK' => [
                    'products' => [
                        [
                            'identifier' => 'accessory-001',
                            'quantity' => 3,
                        ],
                    ],
                    'product_models' => [
                        [
                            'identifier' => 'addon-model-001',
                            'quantity' => 1,
                        ],
                    ],
                ],
            ],
            'created' => '2023-09-15T10:20:30+00:00',
            'updated' => '2023-10-01T14:35:45+00:00',
            'metadata' => [
                'workflow_status' => 'draft',
            ],
            'quality_scores' => [
                [
                    'scope' => 'ecommerce',
                    'locale' => 'en_US',
                    'data' => 'B',
                ],
            ],
            'workflow_execution_statuses' => [
                'workflow_1' => [
                    'status' => 'in_progress',
                    'updated' => '2023-10-01T12:00:00+00:00',
                ],
            ],
            'values' => [
                'collection' => [
                    [
                        'locale' => null,
                        'scope' => null,
                        'data' => 'summer_2024',
                    ],
                ],
                'material' => [
                    [
                        'locale' => 'en_US',
                        'scope' => null,
                        'data' => 'Organic Cotton',
                    ],
                    [
                        'locale' => 'fr_FR',
                        'scope' => null,
                        'data' => 'Coton Biologique',
                    ],
                ],
            ],
        ];

        $model = $this->serializer->denormalize($apiResponse, ProductModel::class);

        expect($model)->toBeInstanceOf(ProductModel::class)
            ->and($model->getCode())->toBe('tshirt-model-master')
            ->and($model->getFamily())->toBe('clothing')
            ->and($model->getFamilyVariant())->toBe('clothing_color_size')
            ->and($model->getCategories())->toBe(['t-shirts', 'summer_collection', 'men'])
            ->and($model->getParent())->toBeNull()
            ->and($model->getCreated())->toBe('2023-09-15T10:20:30+00:00')
            ->and($model->getUpdated())->toBe('2023-10-01T14:35:45+00:00')
            ->and($model->getAssociations())->toHaveKey('SUBSTITUTION')
            ->and($model->getQuantifiedAssociations())->toHaveKey('PACK')
            ->and($model->getValues())->toBeInstanceOf(Values::class);
    });

    it('deserializes a minimal product model', function () {
        $apiResponse = [
            'code' => 'minimal-model',
            'family' => 'accessories',
            'family_variant' => 'accessories_by_color',
            'parent' => null,
            'categories' => [],
            'values' => [],
        ];

        $model = $this->serializer->denormalize($apiResponse, ProductModel::class);

        expect($model)->toBeInstanceOf(ProductModel::class)
            ->and($model->getCode())->toBe('minimal-model')
            ->and($model->getFamily())->toBe('accessories')
            ->and($model->getFamilyVariant())->toBe('accessories_by_color');
    });

    it('deserializes a sub-product model with parent', function () {
        $apiResponse = [
            'code' => 'tshirt-blue-model',
            'family' => 'clothing',
            'family_variant' => 'clothing_color_size',
            'parent' => 'tshirt-master-model',
            'categories' => ['t-shirts'],
            'values' => [
                'color' => [
                    [
                        'locale' => null,
                        'scope' => null,
                        'data' => 'blue',
                    ],
                ],
                'name' => [
                    [
                        'locale' => 'en_US',
                        'scope' => null,
                        'data' => 'Blue T-shirt',
                    ],
                ],
            ],
        ];

        $model = $this->serializer->denormalize($apiResponse, ProductModel::class);

        expect($model->getParent())->toBe('tshirt-master-model')
            ->and($model->getValue('color')->getData())->toBe('blue')
            ->and($model->getValue('name', null, 'en_US')->getData())->toBe('Blue T-shirt');
    });

    it('deserializes multi-locale values for product models', function () {
        $apiResponse = [
            'code' => 'multi-locale-model',
            'family' => 'shoes',
            'family_variant' => 'shoes_by_size',
            'values' => [
                'name' => [
                    [
                        'locale' => 'en_US',
                        'scope' => null,
                        'data' => 'Running Shoe',
                    ],
                    [
                        'locale' => 'fr_FR',
                        'scope' => null,
                        'data' => 'Chaussure de Course',
                    ],
                    [
                        'locale' => 'de_DE',
                        'scope' => null,
                        'data' => 'Laufschuh',
                    ],
                ],
                'description' => [
                    [
                        'locale' => 'en_US',
                        'scope' => 'ecommerce',
                        'data' => 'Professional running shoe',
                    ],
                    [
                        'locale' => 'en_US',
                        'scope' => 'print',
                        'data' => 'High-performance athletic footwear',
                    ],
                ],
            ],
        ];

        $model = $this->serializer->denormalize($apiResponse, ProductModel::class);
        $values = iterator_to_array($model->getValues());

        expect($values)->toHaveCount(5)
            ->and($model->getValue('name', null, 'en_US')->getData())->toBe('Running Shoe')
            ->and($model->getValue('name', null, 'fr_FR')->getData())->toBe('Chaussure de Course')
            ->and($model->getValue('name', null, 'de_DE')->getData())->toBe('Laufschuh')
            ->and($model->getValue('description', 'ecommerce', 'en_US')->getData())->toBe('Professional running shoe')
            ->and($model->getValue('description', 'print', 'en_US')->getData())->toBe('High-performance athletic footwear');
    });

    it('deserializes complex metadata and workflow statuses', function () {
        $apiResponse = [
            'code' => 'workflow-test-model',
            'family' => 'electronics',
            'family_variant' => 'electronics_variant',
            'metadata' => [
                'workflow_status' => 'in_review',
                'created_by' => 'user_123',
                'custom_field' => 'custom_value',
            ],
            'quality_scores' => [
                [
                    'scope' => 'ecommerce',
                    'locale' => 'en_US',
                    'data' => 'A',
                ],
                [
                    'scope' => 'print',
                    'locale' => 'en_US',
                    'data' => 'C',
                ],
            ],
            'workflow_execution_statuses' => [
                'approval_workflow' => [
                    'status' => 'pending_approval',
                    'step' => 'manager_review',
                ],
                'translation_workflow' => [
                    'status' => 'completed',
                    'step' => 'final',
                ],
            ],
        ];

        $model = $this->serializer->denormalize($apiResponse, ProductModel::class);
        $metadata = $model->getMetadata();
        $qualityScores = $model->getQualityScores();
        $workflowStatuses = $model->getWorkflowExecutionStatuses();

        expect($metadata['workflow_status'])->toBe('in_review')
            ->and($metadata['custom_field'])->toBe('custom_value')
            ->and($qualityScores)->toHaveCount(2)
            ->and($qualityScores[0]['data'])->toBe('A')
            ->and($workflowStatuses)->toHaveKey('approval_workflow')
            ->and($workflowStatuses['approval_workflow']['status'])->toBe('pending_approval');
    });
});

describe('ProductModel normalization to Akeneo API format', function () {
    it('normalizes a complete product model to API format', function () {
        $model = new ProductModel('test-model-001');
        $model->setFamily('clothing')
            ->setFamilyVariant('clothing_by_color')
            ->setCategories(['shirts', 'casual'])
            ->setParent(null)
            ->setAssociations([
                'X_SELL' => [
                    'products' => ['related-1'],
                    'groups' => [],
                    'product_models' => ['related-model-1'],
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
            ->setMetadata(['status' => 'draft']);

        $values = new Values;
        $values->upsert(new Value('collection', 'Spring 2024'));
        $model->setValues($values);

        $normalized = $this->serializer->normalize($model);

        expect($normalized)->toBeArray()
            ->and($normalized)->toHaveKey('code')
            ->and($normalized['code'])->toBe('test-model-001')
            ->and($normalized['family'])->toBe('clothing')
            ->and($normalized['family_variant'])->toBe('clothing_by_color')
            ->and($normalized['categories'])->toBe(['shirts', 'casual'])
            ->and($normalized)->toHaveKey('values')
            ->and($normalized)->toHaveKey('associations')
            ->and($normalized)->toHaveKey('quantified_associations');
    });

    it('normalizes values back to Akeneo format', function () {
        $model = new ProductModel('value-norm-test');
        $values = new Values;
        $values->upsert(new Value('material', 'Cotton', null, 'en_US'));
        $values->upsert(new Value('material', 'Coton', null, 'fr_FR'));
        $values->upsert(new Value('care_instructions', 'Machine wash', 'ecommerce', 'en_US'));
        $model->setValues($values);

        $normalized = $this->serializer->normalize($model);

        expect($normalized['values'])->toBeArray()
            ->and($normalized['values'])->toHaveKey('material')
            ->and($normalized['values'])->toHaveKey('care_instructions')
            ->and($normalized['values']['material'])->toHaveCount(2)
            ->and($normalized['values']['care_instructions'])->toHaveCount(1);
    });

    it('uses snake_case for property names in normalized output', function () {
        $model = new ProductModel('snake-case-test');
        $model->setFamilyVariant('test_variant')
            ->setQuantifiedAssociations(['PACK' => ['products' => [], 'product_models' => []]])
            ->setWorkflowExecutionStatuses(['workflow' => ['status' => 'done']]);

        $normalized = $this->serializer->normalize($model);

        expect($normalized)->toHaveKey('family_variant')
            ->and($normalized)->toHaveKey('quantified_associations')
            ->and($normalized)->toHaveKey('workflow_execution_statuses')
            ->and($normalized)->not->toHaveKey('familyVariant')
            ->and($normalized)->not->toHaveKey('quantifiedAssociations');
    });
});

describe('ProductModel round-trip serialization', function () {
    it('maintains data integrity through serialize-deserialize cycle', function () {
        $originalData = [
            'code' => 'roundtrip-model-test',
            'family' => 'accessories',
            'family_variant' => 'accessories_variant',
            'parent' => null,
            'categories' => ['bags', 'leather'],
            'associations' => [
                'SUBSTITUTION' => [
                    'products' => [],
                    'product_models' => ['alt-model-1'],
                    'groups' => [],
                ],
            ],
            'created' => '2024-01-01T10:00:00+00:00',
            'updated' => '2024-01-15T14:30:00+00:00',
            'values' => [
                'name' => [
                    ['locale' => 'en_US', 'scope' => null, 'data' => 'Leather Bag'],
                ],
            ],
        ];

        // Deserialize
        $model = $this->serializer->denormalize($originalData, ProductModel::class);

        // Serialize
        $normalized = $this->serializer->normalize($model);

        // Verify key properties maintained
        expect($normalized['code'])->toBe($originalData['code'])
            ->and($normalized['family'])->toBe($originalData['family'])
            ->and($normalized['family_variant'])->toBe($originalData['family_variant'])
            ->and($normalized['categories'])->toBe($originalData['categories'])
            ->and($normalized['created'])->toBe($originalData['created'])
            ->and($normalized['updated'])->toBe($originalData['updated']);
    });

    it('handles hierarchical product models correctly', function () {
        // Master model
        $masterData = [
            'code' => 'master-model',
            'family' => 'clothing',
            'family_variant' => 'clothing_color_size',
            'parent' => null,
            'values' => [
                'collection' => [
                    ['locale' => null, 'scope' => null, 'data' => 'Winter 2024'],
                ],
            ],
        ];

        // Sub-model (level 1)
        $subModel1Data = [
            'code' => 'master-model-blue',
            'family' => 'clothing',
            'family_variant' => 'clothing_color_size',
            'parent' => 'master-model',
            'values' => [
                'color' => [
                    ['locale' => null, 'scope' => null, 'data' => 'blue'],
                ],
            ],
        ];

        // Sub-model (level 2)
        $subModel2Data = [
            'code' => 'master-model-blue-m',
            'family' => 'clothing',
            'family_variant' => 'clothing_color_size',
            'parent' => 'master-model-blue',
            'values' => [
                'size' => [
                    ['locale' => null, 'scope' => null, 'data' => 'M'],
                ],
            ],
        ];

        $master = $this->serializer->denormalize($masterData, ProductModel::class);
        $sub1 = $this->serializer->denormalize($subModel1Data, ProductModel::class);
        $sub2 = $this->serializer->denormalize($subModel2Data, ProductModel::class);

        expect($master->getParent())->toBeNull()
            ->and($sub1->getParent())->toBe('master-model')
            ->and($sub2->getParent())->toBe('master-model-blue')
            ->and($master->getValue('collection')->getData())->toBe('Winter 2024')
            ->and($sub1->getValue('color')->getData())->toBe('blue')
            ->and($sub2->getValue('size')->getData())->toBe('M');
    });

    it('preserves empty and null values correctly', function () {
        $originalData = [
            'code' => 'empty-test',
            'family' => 'test_family',
            'family_variant' => 'test_variant',
            'parent' => null,
            'categories' => [],
            'associations' => [],
            'quantified_associations' => [],
            'metadata' => null,
            'values' => [],
        ];

        $model = $this->serializer->denormalize($originalData, ProductModel::class);
        $normalized = $this->serializer->normalize($model);

        expect($model->getParent())->toBeNull()
            ->and($model->getMetadata())->toBeNull()
            ->and($model->getCategories())->toBe([]);
    });
});
