<?php

declare(strict_types=1);

use AkeneoLib\Adapter\BatchableAdapterTrait;

// Minimal concrete class using the trait for testing purposes
class ConcreteBatchableAdapter
{
    use BatchableAdapterTrait {
        addPendingItem as public;
        hasPendingItems as public;
        clearPendingItems as public;
        isPendingBatchFull as public;
        triggerResponseCallback as public;
    }
}

describe('BatchableAdapterTrait', function () {
    beforeEach(function () {
        $this->adapter = new ConcreteBatchableAdapter;
    });

    it('has default batch size of 100', function () {
        expect($this->adapter->getBatchSize())->toBe(100);
    });

    it('sets and gets batch size', function () {
        $this->adapter->setBatchSize(50);
        expect($this->adapter->getBatchSize())->toBe(50);
    });

    it('setBatchSize returns self for chaining', function () {
        expect($this->adapter->setBatchSize(25))->toBe($this->adapter);
    });

    it('onResponse returns self for chaining', function () {
        expect($this->adapter->onResponse(fn () => null))->toBe($this->adapter);
    });

    it('has no pending items initially', function () {
        expect($this->adapter->hasPendingItems())->toBeFalse();
    });

    it('detects pending items after adding', function () {
        $this->adapter->addPendingItem(new stdClass);
        expect($this->adapter->hasPendingItems())->toBeTrue();
    });

    it('clears pending items', function () {
        $this->adapter->addPendingItem(new stdClass);
        $this->adapter->clearPendingItems();
        expect($this->adapter->hasPendingItems())->toBeFalse();
    });

    it('is not full when items are below batch size', function () {
        $this->adapter->setBatchSize(3);
        $this->adapter->addPendingItem(new stdClass);
        $this->adapter->addPendingItem(new stdClass);
        expect($this->adapter->isPendingBatchFull())->toBeFalse();
    });

    it('is full when items reach batch size', function () {
        $this->adapter->setBatchSize(2);
        $this->adapter->addPendingItem(new stdClass);
        $this->adapter->addPendingItem(new stdClass);
        expect($this->adapter->isPendingBatchFull())->toBeTrue();
    });

    it('triggerResponseCallback fires registered callback with correct args', function () {
        $capturedArgs = null;
        $this->adapter->onResponse(function () use (&$capturedArgs) {
            $capturedArgs = func_get_args();
        });

        $response = new ArrayIterator(['result1']);
        $pushed = [new stdClass];

        $this->adapter->triggerResponseCallback($response, $pushed);

        expect($capturedArgs)->toHaveCount(3)
            ->and($capturedArgs[0])->toBe($response)
            ->and($capturedArgs[1])->toBe($pushed)
            ->and($capturedArgs[2])->toBeInstanceOf(DateTimeImmutable::class);
    });

    it('triggerResponseCallback does nothing when no callback registered', function () {
        // Should not throw
        $this->adapter->triggerResponseCallback(new ArrayIterator([]), []);
        expect(true)->toBeTrue();
    });

    it('triggerResponseCallback accepts array response', function () {
        $capturedResponse = null;
        $this->adapter->onResponse(function ($response) use (&$capturedResponse) {
            $capturedResponse = $response;
        });

        $response = ['item1', 'item2'];
        $this->adapter->triggerResponseCallback($response, []);

        expect($capturedResponse)->toBe($response);
    });

    it('multiple items can be added and all are cleared at once', function () {
        $this->adapter->addPendingItem(new stdClass);
        $this->adapter->addPendingItem(new stdClass);
        $this->adapter->addPendingItem(new stdClass);

        expect($this->adapter->hasPendingItems())->toBeTrue();

        $this->adapter->clearPendingItems();

        expect($this->adapter->hasPendingItems())->toBeFalse();
    });
});
