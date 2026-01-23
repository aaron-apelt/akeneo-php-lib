<?php

declare(strict_types=1);

namespace AkeneoLib\Adapter\Support;

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
        foreach ($this->items as $key => $item) {
            $callback($item, $key);
        }

        return $this;
    }

    public function take(int $limit): static
    {
        $gen = function () use ($limit) {
            $count = 0;
            foreach ($this->items as $key => $item) {
                if ($count++ === $limit) {
                    break;
                }
                yield $key => $item;
            }
        };

        return new static($gen());
    }

    public function skip(int $count): static
    {
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

            return null;
        }

        $found = null;
        foreach ($this->items as $key => $item) {
            if ($callback === null || $callback($item, $key)) {
                $found = $item;
            }
        }

        return $found;
    }

    public function toArray(): array
    {
        if (is_array($this->items)) {
            return $this->items;
        }

        return iterator_to_array($this->items, true);
    }

    public function chunk(int $size): static
    {
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

    public function sort(callable $callback): static
    {
        $arr = $this->toArray();
        uasort($arr, $callback);

        return new static($arr);
    }

    public function unique(?callable $callback = null): static
    {
        $result = [];
        $seen = [];
        foreach ($this->items as $key => $item) {
            $val = $callback ? $callback($item, $key) : $item;
            if (in_array($val, $seen, true)) {
                continue;
            }
            $seen[] = $val;
            $result[$key] = $item;
        }

        return new static($result);
    }
}
