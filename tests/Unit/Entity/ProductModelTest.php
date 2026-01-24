<?php

declare(strict_types=1);

use AkeneoLib\Entity\ProductModel;
use AkeneoLib\Entity\Value;
use AkeneoLib\Entity\Values;

describe('identifier management', function () {
    it('can get the identifier', function () {
        $model = new ProductModel('tshirt-master');
        expect($model->getIdentifier())->toBe('tshirt-master');
    });

    it('can set the identifier', function () {
        $model = new ProductModel('old_id')->setIdentifier('new_id');
        expect($model->getIdentifier())->toBe('new_id');
    });
});

describe('enabled status management', function () {
    it('can get the enabled status (initially null)', function () {
        $model = new ProductModel('model');
        expect($model->isEnabled())->toBeNull();
    });

    it('can set the enabled status to true', function () {
        $model = new ProductModel('model')->setEnabled(true);
        expect($model->isEnabled())->toBeTrue();
    });

    it('can set the enabled status to false', function () {
        $model = new ProductModel('model')->setEnabled(false);
        expect($model->isEnabled())->toBeFalse();
    });

    it('can set the enabled status to null', function () {
        $model = new ProductModel('model')->setEnabled(true)->setEnabled(null);
        expect($model->isEnabled())->toBeNull();
    });
});

describe('family management', function () {
    it('can get the family (initially null)', function () {
        $model = new ProductModel('model');
        expect($model->getFamily())->toBeNull();
    });

    it('can set the family', function () {
        $model = new ProductModel('model')->setFamily('clothing');
        expect($model->getFamily())->toBe('clothing');
    });

    it('can set the family to null', function () {
        $model = new ProductModel('model')->setFamily('clothing')->setFamily(null);
        expect($model->getFamily())->toBeNull();
    });
});

describe('family variant management', function () {
    it('can get the family variant (initially null)', function () {
        $model = new ProductModel('model');
        expect($model->getFamilyVariant())->toBeNull();
    });

    it('can set the family variant', function () {
        $model = new ProductModel('model')->setFamilyVariant('by_size');
        expect($model->getFamilyVariant())->toBe('by_size');
    });

    it('can set the family variant to null', function () {
        $model = new ProductModel('model')->setFamilyVariant('by_size')->setFamilyVariant(null);
        expect($model->getFamilyVariant())->toBeNull();
    });
});

describe('categories management', function () {
    it('can get the categories (initially null)', function () {
        $model = new ProductModel('model');
        expect($model->getCategories())->toBeNull();
    });

    it('can set the categories', function () {
        $categories = ['tshirts', 'clothing'];
        $model = new ProductModel('model')->setCategories($categories);
        expect($model->getCategories())->toBe($categories);
    });

    it('can set the categories to null', function () {
        $model = new ProductModel('model')->setCategories(['test'])->setCategories(null);
        expect($model->getCategories())->toBeNull();
    });
});

describe('parent management', function () {
    it('can get the parent (initially null)', function () {
        $model = new ProductModel('model');
        expect($model->getParent())->toBeNull();
    });

    it('can set the parent', function () {
        $model = new ProductModel('model')->setParent('parent_model');
        expect($model->getParent())->toBe('parent_model');
    });

    it('can set the parent to null', function () {
        $model = new ProductModel('model')->setParent('parent_model')->setParent(null);
        expect($model->getParent())->toBeNull();
    });
});

describe('associations management', function () {
    it('can get the associations (initially null)', function () {
        $model = new ProductModel('model');
        expect($model->getAssociations())->toBeNull();
    });

    it('can set the associations', function () {
        $associations = ['PACK' => ['products' => ['product1'], 'groups' => []]];
        $model = new ProductModel('model')->setAssociations($associations);
        expect($model->getAssociations())->toBe($associations);
    });

    it('can set the associations to null', function () {
        $model = new ProductModel('model')->setAssociations(['X_SELL' => []])->setAssociations(null);
        expect($model->getAssociations())->toBeNull();
    });
});

describe('quantified associations management', function () {
    it('can get the quantified associations (initially null)', function () {
        $model = new ProductModel('model');
        expect($model->getQuantifiedAssociations())->toBeNull();
    });

    it('can set the quantified associations', function () {
        $quantifiedAssociations = ['PRODUCT_SET' => ['products' => [['identifier' => 'abc', 'quantity' => 2]]]];
        $model = new ProductModel('model')->setQuantifiedAssociations($quantifiedAssociations);
        expect($model->getQuantifiedAssociations())->toBe($quantifiedAssociations);
    });

    it('can set the quantified associations to null', function () {
        $model = new ProductModel('model')->setQuantifiedAssociations(['SET' => []])->setQuantifiedAssociations(null);
        expect($model->getQuantifiedAssociations())->toBeNull();
    });
});

describe('created management', function () {
    it('can get the created timestamp (initially null)', function () {
        $model = new ProductModel('model');
        expect($model->getCreated())->toBeNull();
    });

    it('can set the created timestamp', function () {
        $model = new ProductModel('model')->setCreated('2016-06-23T18:24:44+02:00');
        expect($model->getCreated())->toBe('2016-06-23T18:24:44+02:00');
    });

    it('can set the created timestamp to null', function () {
        $model = new ProductModel('model')->setCreated('2020-01-01T00:00:00+00:00')->setCreated(null);
        expect($model->getCreated())->toBeNull();
    });
});

describe('updated management', function () {
    it('can get the updated timestamp (initially null)', function () {
        $model = new ProductModel('model');
        expect($model->getUpdated())->toBeNull();
    });

    it('can set the updated timestamp', function () {
        $model = new ProductModel('model')->setUpdated('2016-06-25T17:56:12+02:00');
        expect($model->getUpdated())->toBe('2016-06-25T17:56:12+02:00');
    });

    it('can set the updated timestamp to null', function () {
        $model = new ProductModel('model')->setUpdated('2020-01-01T00:00:00+00:00')->setUpdated(null);
        expect($model->getUpdated())->toBeNull();
    });
});

describe('metadata management', function () {
    it('can get the metadata (initially null)', function () {
        $model = new ProductModel('model');
        expect($model->getMetadata())->toBeNull();
    });

    it('can set the metadata', function () {
        $metadata = ['workflow_status' => 'working_copy'];
        $model = new ProductModel('model')->setMetadata($metadata);
        expect($model->getMetadata())->toBe($metadata);
    });

    it('can set the metadata to null', function () {
        $model = new ProductModel('model')->setMetadata(['key' => 'value'])->setMetadata(null);
        expect($model->getMetadata())->toBeNull();
    });
});

describe('quality scores management', function () {
    it('can get the quality scores (initially null)', function () {
        $model = new ProductModel('model');
        expect($model->getQualityScores())->toBeNull();
    });

    it('can set the quality scores', function () {
        $qualityScores = [['scope' => 'ecommerce', 'locale' => 'en_US', 'data' => 'A']];
        $model = new ProductModel('model')->setQualityScores($qualityScores);
        expect($model->getQualityScores())->toBe($qualityScores);
    });

    it('can set the quality scores to null', function () {
        $model = new ProductModel('model')->setQualityScores([])->setQualityScores(null);
        expect($model->getQualityScores())->toBeNull();
    });
});

describe('workflow execution statuses management', function () {
    it('can get the workflow execution statuses (initially null)', function () {
        $model = new ProductModel('model');
        expect($model->getWorkflowExecutionStatuses())->toBeNull();
    });

    it('can set the workflow execution statuses', function () {
        $statuses = [['status' => 'in_progress', 'step' => 'review']];
        $model = new ProductModel('model')->setWorkflowExecutionStatuses($statuses);
        expect($model->getWorkflowExecutionStatuses())->toBe($statuses);
    });

    it('can set the workflow execution statuses to null', function () {
        $statuses = [['status' => 'completed']];
        $model = new ProductModel('model')->setWorkflowExecutionStatuses($statuses)->setWorkflowExecutionStatuses(null);
        expect($model->getWorkflowExecutionStatuses())->toBeNull();
    });
});

describe('values management', function () {
    it('can get the values (initially null)', function () {
        $model = new ProductModel('model');
        expect($model->getValues())->toBeNull();
    });

    it('can set the values', function () {
        $values = new Values;
        $model = new ProductModel('model')->setValues($values);
        expect($model->getValues())->toBe($values);
    });

    it('can set the values to null', function () {
        $model = new ProductModel('model')->setValues(new Values)->setValues(null);
        expect($model->getValues())->toBeNull();
    });
});

describe('value management', function () {
    it('can upsert a value and initialize values if null', function () {
        $model = new ProductModel('model');
        $value = new Value('name', 'My Model');
        $model->upsertValue($value);
        expect($model->getValues())->toBeInstanceOf(Values::class)
            ->and($model->getValue('name'))->toBe($value);
    });

    it('can upsert a value when values already exist', function () {
        $model = new ProductModel('model');
        $initialValue = new Value('description', 'Initial description');
        $model->setValues((new Values)->upsert($initialValue));
        $newValue = new Value('description', 'Updated description');
        $model->upsertValue($newValue);
        expect($model->getValue('description'))->toBe($newValue);
    });

    it('can get a specific value by code', function () {
        $model = new ProductModel('model');
        $value1 = new Value('color', 'red');
        $value2 = new Value('size', 'M');
        $model->setValues((new Values)->upsert($value1)->upsert($value2));
        expect($model->getValue('color'))->toBe($value1)
            ->and($model->getValue('size'))->toBe($value2)
            ->and($model->getValue('non_existent'))->toBeNull();
    });

    it('can get a specific value by code, scope, and locale', function () {
        $model = new ProductModel('model');
        $value1 = new Value('price', 10.99, 'ecommerce', 'en_US');
        $value2 = new Value('price', 12.50, 'print', 'de_DE');
        $model->setValues((new Values)->upsert($value1)->upsert($value2));
        expect($model->getValue('price', 'ecommerce', 'en_US'))->toBe($value1)
            ->and($model->getValue('price', 'print', 'de_DE'))->toBe($value2)
            ->and($model->getValue('price', 'ecommerce', 'de_DE'))->toBeNull();
    });
});
