<?php

declare(strict_types=1);

use AkeneoLib\Entity\Product;
use AkeneoLib\Entity\Value;
use AkeneoLib\Entity\Values;

describe('identifier management', function () {
    it('can get the identifier', function () {
        $product = new Product('initial_id');
        expect($product->getIdentifier())->toBe('initial_id');
    });

    it('can set the identifier', function () {
        $product = new Product('old_id')->setIdentifier('new_id');
        expect($product->getIdentifier())->toBe('new_id');
    });
});

describe('enabled status management', function () {
    it('can get the enabled status (initially null)', function () {
        $product = new Product('id');
        expect($product->isEnabled())->toBeNull();
    });

    it('can set the enabled status to true', function () {
        $product = new Product('id')->setEnabled(true);
        expect($product->isEnabled())->toBeTrue();
    });

    it('can set the enabled status to false', function () {
        $product = new Product('id')->setEnabled(false);
        expect($product->isEnabled())->toBeFalse();
    });

    it('can set the enabled status to null', function () {
        $product = new Product('id')->setEnabled(true)->setEnabled(null);
        expect($product->isEnabled())->toBeNull();
    });
});

describe('family management', function () {
    it('can get the family (initially null)', function () {
        $product = new Product('id');
        expect($product->getFamily())->toBeNull();
    });

    it('can set the family', function () {
        $product = new Product('id')->setFamily('clothing');
        expect($product->getFamily())->toBe('clothing');
    });

    it('can set the family to null', function () {
        $product = new Product('id')->setFamily('electronics')->setFamily(null);
        expect($product->getFamily())->toBeNull();
    });
});

describe('categories management', function () {
    it('can get the categories (initially null)', function () {
        $product = new Product('id');
        expect($product->getCategories())->toBeNull();
    });

    it('can set the categories', function () {
        $categories = ['t-shirts', 'summer'];
        $product = new Product('id')->setCategories($categories);
        expect($product->getCategories())->toBe($categories);
    });

    it('can set the categories to null', function () {
        $product = new Product('id')->setCategories(['shoes'])->setCategories(null);
        expect($product->getCategories())->toBeNull();
    });
});

describe('parent management', function () {
    it('can get the parent (initially null)', function () {
        $product = new Product('id');
        expect($product->getParent())->toBeNull();
    });

    it('can set the parent', function () {
        $product = new Product('id')->setParent('master_product');
        expect($product->getParent())->toBe('master_product');
    });

    it('can set the parent to null', function () {
        $product = new Product('id')->setParent('another_master')->setParent(null);
        expect($product->getParent())->toBeNull();
    });
});

describe('uuid management', function () {
    it('can get the uuid (initially null)', function () {
        $product = new Product('id');
        expect($product->getUuid())->toBeNull();
    });

    it('can set the uuid', function () {
        $product = new Product('id')->setUuid('3fa85f64-5717-4562-b3fc-2c963f66afa6');
        expect($product->getUuid())->toBe('3fa85f64-5717-4562-b3fc-2c963f66afa6');
    });

    it('can set the uuid to null', function () {
        $product = new Product('id')->setUuid('uuid-123')->setUuid(null);
        expect($product->getUuid())->toBeNull();
    });
});

describe('groups management', function () {
    it('can get the groups (initially null)', function () {
        $product = new Product('id');
        expect($product->getGroups())->toBeNull();
    });

    it('can set the groups', function () {
        $groups = ['winter_collection', 'promotion'];
        $product = new Product('id')->setGroups($groups);
        expect($product->getGroups())->toBe($groups);
    });

    it('can set the groups to null', function () {
        $product = new Product('id')->setGroups(['group1'])->setGroups(null);
        expect($product->getGroups())->toBeNull();
    });
});

describe('associations management', function () {
    it('can get the associations (initially null)', function () {
        $product = new Product('id');
        expect($product->getAssociations())->toBeNull();
    });

    it('can set the associations', function () {
        $associations = ['PACK' => ['products' => ['product1'], 'groups' => []]];
        $product = new Product('id')->setAssociations($associations);
        expect($product->getAssociations())->toBe($associations);
    });

    it('can set the associations to null', function () {
        $product = new Product('id')->setAssociations(['X_SELL' => []])->setAssociations(null);
        expect($product->getAssociations())->toBeNull();
    });
});

describe('quantified associations management', function () {
    it('can get the quantified associations (initially null)', function () {
        $product = new Product('id');
        expect($product->getQuantifiedAssociations())->toBeNull();
    });

    it('can set the quantified associations', function () {
        $quantifiedAssociations = ['PRODUCT_SET' => ['products' => [['uuid' => 'abc', 'quantity' => 2]]]];
        $product = new Product('id')->setQuantifiedAssociations($quantifiedAssociations);
        expect($product->getQuantifiedAssociations())->toBe($quantifiedAssociations);
    });

    it('can set the quantified associations to null', function () {
        $product = new Product('id')->setQuantifiedAssociations(['SET' => []])->setQuantifiedAssociations(null);
        expect($product->getQuantifiedAssociations())->toBeNull();
    });
});

describe('created management', function () {
    it('can get the created timestamp (initially null)', function () {
        $product = new Product('id');
        expect($product->getCreated())->toBeNull();
    });

    it('can set the created timestamp', function () {
        $product = new Product('id')->setCreated('2016-06-23T18:24:44+02:00');
        expect($product->getCreated())->toBe('2016-06-23T18:24:44+02:00');
    });

    it('can set the created timestamp to null', function () {
        $product = new Product('id')->setCreated('2020-01-01T00:00:00+00:00')->setCreated(null);
        expect($product->getCreated())->toBeNull();
    });
});

describe('updated management', function () {
    it('can get the updated timestamp (initially null)', function () {
        $product = new Product('id');
        expect($product->getUpdated())->toBeNull();
    });

    it('can set the updated timestamp', function () {
        $product = new Product('id')->setUpdated('2016-06-25T17:56:12+02:00');
        expect($product->getUpdated())->toBe('2016-06-25T17:56:12+02:00');
    });

    it('can set the updated timestamp to null', function () {
        $product = new Product('id')->setUpdated('2020-01-01T00:00:00+00:00')->setUpdated(null);
        expect($product->getUpdated())->toBeNull();
    });
});

describe('metadata management', function () {
    it('can get the metadata (initially null)', function () {
        $product = new Product('id');
        expect($product->getMetadata())->toBeNull();
    });

    it('can set the metadata', function () {
        $metadata = ['workflow_status' => 'working_copy'];
        $product = new Product('id')->setMetadata($metadata);
        expect($product->getMetadata())->toBe($metadata);
    });

    it('can set the metadata to null', function () {
        $product = new Product('id')->setMetadata(['key' => 'value'])->setMetadata(null);
        expect($product->getMetadata())->toBeNull();
    });
});

describe('quality scores management', function () {
    it('can get the quality scores (initially null)', function () {
        $product = new Product('id');
        expect($product->getQualityScores())->toBeNull();
    });

    it('can set the quality scores', function () {
        $qualityScores = [['scope' => 'ecommerce', 'locale' => 'en_US', 'data' => 'A']];
        $product = new Product('id')->setQualityScores($qualityScores);
        expect($product->getQualityScores())->toBe($qualityScores);
    });

    it('can set the quality scores to null', function () {
        $product = new Product('id')->setQualityScores([])->setQualityScores(null);
        expect($product->getQualityScores())->toBeNull();
    });
});

describe('completenesses management', function () {
    it('can get the completenesses (initially null)', function () {
        $product = new Product('id');
        expect($product->getCompletenesses())->toBeNull();
    });

    it('can set the completenesses', function () {
        $completenesses = [['scope' => 'ecommerce', 'locale' => 'en_US', 'data' => 10]];
        $product = new Product('id')->setCompletenesses($completenesses);
        expect($product->getCompletenesses())->toBe($completenesses);
    });

    it('can set the completenesses to null', function () {
        $product = new Product('id')->setCompletenesses([])->setCompletenesses(null);
        expect($product->getCompletenesses())->toBeNull();
    });
});

describe('root parent management', function () {
    it('can get the root parent (initially null)', function () {
        $product = new Product('id');
        expect($product->getRootParent())->toBeNull();
    });

    it('can set the root parent', function () {
        $product = new Product('id')->setRootParent('root_product');
        expect($product->getRootParent())->toBe('root_product');
    });

    it('can set the root parent to null', function () {
        $product = new Product('id')->setRootParent('root_product')->setRootParent(null);
        expect($product->getRootParent())->toBeNull();
    });
});

describe('workflow execution status management', function () {
    it('can get the workflow execution status (initially null)', function () {
        $product = new Product('id');
        expect($product->getWorkflowExecutionStatus())->toBeNull();
    });

    it('can set the workflow execution status', function () {
        $status = ['status' => 'in_progress', 'step' => 'review'];
        $product = new Product('id')->setWorkflowExecutionStatus($status);
        expect($product->getWorkflowExecutionStatus())->toBe($status);
    });

    it('can set the workflow execution status to null', function () {
        $status = ['status' => 'completed'];
        $product = new Product('id')->setWorkflowExecutionStatus($status)->setWorkflowExecutionStatus(null);
        expect($product->getWorkflowExecutionStatus())->toBeNull();
    });
});

describe('values management', function () {
    it('can get the values (initially null)', function () {
        $product = new Product('id');
        expect($product->getValues())->toBeNull();
    });

    it('can set the values', function () {
        $values = new Values;
        $product = new Product('id')->setValues($values);
        expect($product->getValues())->toBe($values);
    });

    it('can set the values to null', function () {
        $product = new Product('id')->setValues(new Values)->setValues(null);
        expect($product->getValues())->toBeNull();
    });
});

describe('value management', function () {
    it('can upsert a value and initialize values if null', function () {
        $product = new Product('id');
        $value = new Value('name', 'My Product');
        $product->upsertValue($value);
        expect($product->getValues())->toBeInstanceOf(Values::class)
            ->and($product->getValue('name'))->toBe($value);
    });

    it('can upsert a value when values already exist', function () {
        $product = new Product('id');
        $initialValue = new Value('description', 'Initial description');
        $product->setValues((new Values)->upsert($initialValue));
        $newValue = new Value('description', 'Updated description');
        $product->upsertValue($newValue);
        expect($product->getValue('description'))->toBe($newValue);
    });

    it('can get a specific value by code', function () {
        $product = new Product('id');
        $value1 = new Value('color', 'red');
        $value2 = new Value('size', 'M');
        $product->setValues((new Values)->upsert($value1)->upsert($value2));
        expect($product->getValue('color'))->toBe($value1)
            ->and($product->getValue('size'))->toBe($value2)
            ->and($product->getValue('non_existent'))->toBeNull();
    });

    it('can get a specific value by code, scope, and locale', function () {
        $product = new Product('id');
        $value1 = new Value('price', 10.99, 'ecommerce', 'en_US');
        $value2 = new Value('price', 12.50, 'print', 'de_DE');
        $product->setValues((new Values)->upsert($value1)->upsert($value2));
        expect($product->getValue('price', 'ecommerce', 'en_US'))->toBe($value1)
            ->and($product->getValue('price', 'print', 'de_DE'))->toBe($value2)
            ->and($product->getValue('price', 'ecommerce', 'de_DE'))->toBeNull();
    });
});
