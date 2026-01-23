<?php

declare(strict_types=1);

namespace AkeneoLib\Adapter\Support;

use InvalidArgumentException;
use IteratorAggregate;
use OutOfBoundsException;
use Traversable;

class FluentAdapterResult implements FluentAdapterResultInterface, IteratorAggregate
{
    private iterable $items;

    public function __construct(iterable $items)
    {
        $this->items = $items;
    }

    public function getIterator(): Traversable
    {
        yield from $this->items;
    }

    public function filter(callable $callback): static
    {
        $gen = function () use ($callback) {
            foreach ($this->items as $key => $item) {
                if ($callback($item, $key)) {
                    yield $key => $item;
                }
            }
        };

        return new static($gen());
    }

    public function map(callable $callback): static
    {
        $gen = function () use ($callback) {
            foreach ($this->items as $key => $item) {
                yield $key => $callback($item, $key);
            }
        };

        return new static($gen());
    }

    /**
     * Reduce the collection to a single value using a callback.
     *
     * WARNING: This is a terminal operation that iterates through the entire collection.
     * When working with large datasets (e.g., API cursors), this will process all items.
     *
     * @param  callable  $callback  Function to reduce items: fn($carry, $item, $key)
     * @param  mixed  $initial  Initial value for the accumulator
     */
    public function reduce(callable $callback, mixed $initial): mixed
    {
        $acc = $initial;
        foreach ($this->items as $key => $item) {
            $acc = $callback($acc, $item, $key);
        }

        return $acc;
    }

    public function each(callable $callback): static
    {
        $gen = function () use ($callback) {
            foreach ($this->items as $key => $item) {
                $callback($item, $key);
                yield $key => $item;
            }
        };

        return new static($gen());
    }

    public function take(int $limit): static
    {
        if ($limit < 0) {
            throw new InvalidArgumentException('Take limit must be non-negative.');
        }

        $gen = function () use ($limit) {
            $count = 0;
            foreach ($this->items as $key => $item) {
                if ($count >= $limit) {
                    break;
                }
                yield $key => $item;
                $count++;
            }
        };

        return new static($gen());
    }

    public function skip(int $count): static
    {
        if ($count < 0) {
            throw new InvalidArgumentException('Skip count must be non-negative.');
        }

        $gen = function () use ($count) {
            $i = 0;
            foreach ($this->items as $key => $item) {
                if ($i++ < $count) {
                    continue;
                }
                yield $key => $item;
            }
        };

        return new static($gen());
    }

    public function first(?callable $callback = null)
    {
        foreach ($this->items as $key => $item) {
            if ($callback === null || $callback($item, $key)) {
                return $item;
            }
        }

        throw new OutOfBoundsException('No matching item found in FluentAdapterResult.');
    }

    public function last(?callable $callback = null)
    {
        if (is_array($this->items)) {
            foreach (array_reverse($this->items, true) as $key => $item) {
                if ($callback === null || $callback($item, $key)) {
                    return $item;
                }
            }

            throw new OutOfBoundsException('No matching item found in FluentAdapterResult.');
        }

        $found = null;
        foreach ($this->items as $key => $item) {
            if ($callback === null || $callback($item, $key)) {
                $found = $item;
            }
        }

        if ($found === null) {
            throw new OutOfBoundsException('No matching item found in FluentAdapterResult.');
        }

        return $found;
    }

    /**
     * Convert the collection to an array.
     *
     * WARNING: This is a terminal operation that materializes the entire collection
     * into memory. When working with large datasets (e.g., API cursors), this will
     * load all items at once, which may cause memory issues. Use with caution on
     * large collections.
     */
    public function toArray(): array
    {
        if (is_array($this->items)) {
            return $this->items;
        }

        return iterator_to_array($this->items, true);
    }

    public function chunk(int $size): static
    {
        if ($size < 1) {
            throw new InvalidArgumentException('Chunk size must be at least 1.');
        }

        $gen = function () use ($size) {
            $chunk = [];
            foreach ($this->items as $key => $item) {
                $chunk[$key] = $item;
                if (count($chunk) === $size) {
                    yield $chunk;
                    $chunk = [];
                }
            }
            if ($chunk) {
                yield $chunk;
            }
        };

        return new static($gen());
    }

    /**
     * Sort the items using a comparison callback.
     *
     * WARNING: This is a non-lazy operation that materializes the entire collection
     * into memory. When working with large datasets (e.g., API cursors), this will
     * load all items at once, which may cause memory issues. Use with caution on
     * large collections.
     *
     * @param  callable  $callback  Comparison function that returns <0, 0, or >0
     */
    public function sort(callable $callback): static
    {
        $arr = $this->toArray();
        uasort($arr, $callback);

        return new static($arr);
    }

    /**
     * Filter to unique items based on the callback result or item value.
     *
     * WARNING: This is a non-lazy operation that materializes the entire collection
     * into memory. When working with large datasets (e.g., API cursors), this will
     * load all items at once, which may cause memory issues. Use with caution on
     * large collections.
     *
     * @param  callable|null  $callback  Optional callback to determine uniqueness
     */
    public function unique(?callable $callback = null): static
    {
        $result = [];
        $seen = [];
        foreach ($this->items as $key => $item) {
            $val = $callback ? $callback($item, $key) : $item;

            $lookupKey = is_scalar($val) || $val === null
                ? $val
                : serialize($val);

            if (isset($seen[$lookupKey])) {
                continue;
            }

            $seen[$lookupKey] = true;
            $result[$key] = $item;
        }

        return new static($result);
    }
}
