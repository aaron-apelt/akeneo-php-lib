<?php

declare(strict_types=1);

namespace AkeneoLib\Adapter;

use DateTimeImmutable;

trait BatchableAdapterTrait
{
    protected array $pendingItems = [];

    private int $batchSize = 100;

    /** @var callable|null */
    private $responseCallback = null;

    public function getBatchSize(): int
    {
        return $this->batchSize;
    }

    public function setBatchSize(int $batchSize): self
    {
        $this->batchSize = $batchSize;

        return $this;
    }

    public function onResponse(callable $callback): self
    {
        $this->responseCallback = $callback;

        return $this;
    }

    protected function addPendingItem(object $item): void
    {
        $this->pendingItems[] = $item;
    }

    protected function isPendingBatchFull(): bool
    {
        return count($this->pendingItems) >= $this->batchSize;
    }

    protected function hasPendingItems(): bool
    {
        return ! empty($this->pendingItems);
    }

    protected function clearPendingItems(): void
    {
        $this->pendingItems = [];
    }

    protected function triggerResponseCallback(iterable $response, array $pushedItems): void
    {
        if ($this->responseCallback !== null) {
            call_user_func($this->responseCallback, $response, $pushedItems, new DateTimeImmutable);
        }
    }
}
