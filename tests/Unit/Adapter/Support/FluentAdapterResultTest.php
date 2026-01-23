<?php

declare(strict_types=1);

use AkeneoLib\Adapter\Support\FluentAdapterResult;

describe('FluentAdapterResult', function () {
    it('implements IteratorAggregate and can be iterated', function () {
        $items = [1, 2, 3];
        $result = new FluentAdapterResult($items);

        $iterated = [];
        foreach ($result as $item) {
            $iterated[] = $item;
        }

        expect($iterated)->toBe([1, 2, 3]);
    });

    describe('filter', function () {
        it('filters items based on callback', function () {
            $items = [1, 2, 3, 4, 5];
            $result = new FluentAdapterResult($items);

            $filtered = $result->filter(fn ($item) => $item > 2)->toArray();

            expect($filtered)->toBe([2 => 3, 3 => 4, 4 => 5]);
        });

        it('maintains lazy evaluation with generators', function () {
            $gen = function () {
                yield 1;
                yield 2;
                yield 3;
            };

            $result = new FluentAdapterResult($gen());
            $filtered = $result->filter(fn ($x) => $x > 1);

            expect(iterator_to_array($filtered))->toBe([1 => 2, 2 => 3]);
        });

        it('preserves keys', function () {
            $items = ['a' => 1, 'b' => 2, 'c' => 3];
            $result = new FluentAdapterResult($items);

            $filtered = $result->filter(fn ($item) => $item > 1)->toArray();

            expect($filtered)->toBe(['b' => 2, 'c' => 3]);
        });
    });

    describe('map', function () {
        it('transforms items based on callback', function () {
            $items = [1, 2, 3];
            $result = new FluentAdapterResult($items);

            $mapped = $result->map(fn ($item) => $item * 2)->toArray();

            expect($mapped)->toBe([2, 4, 6]);
        });

        it('maintains lazy evaluation', function () {
            $gen = function () {
                yield 1;
                yield 2;
            };

            $result = new FluentAdapterResult($gen());
            $mapped = $result->map(fn ($x) => $x * 10);

            expect(iterator_to_array($mapped))->toBe([10, 20]);
        });

        it('provides key to callback', function () {
            $items = ['a' => 1, 'b' => 2];
            $result = new FluentAdapterResult($items);

            $mapped = $result->map(fn ($item, $key) => "$key:$item")->toArray();

            expect($mapped)->toBe(['a' => 'a:1', 'b' => 'b:2']);
        });
    });

    describe('reduce', function () {
        it('reduces items to a single value', function () {
            $items = [1, 2, 3, 4];
            $result = new FluentAdapterResult($items);

            $sum = $result->reduce(fn ($acc, $item) => $acc + $item, 0);

            expect($sum)->toBe(10);
        });

        it('works with non-numeric values', function () {
            $items = ['a', 'b', 'c'];
            $result = new FluentAdapterResult($items);

            $concatenated = $result->reduce(fn ($acc, $item) => $acc.$item, '');

            expect($concatenated)->toBe('abc');
        });

        it('provides key to callback', function () {
            $items = ['x' => 1, 'y' => 2];
            $result = new FluentAdapterResult($items);

            $result = $result->reduce(fn ($acc, $item, $key) => $acc."$key:$item,", '');

            expect($result)->toBe('x:1,y:2,');
        });
    });

    describe('each', function () {
        it('executes callback for each item', function () {
            $items = [1, 2, 3];
            $result = new FluentAdapterResult($items);

            $sideEffects = [];
            $chained = $result->each(function ($item) use (&$sideEffects) {
                $sideEffects[] = $item * 2;
            });

            iterator_to_array($chained);

            expect($sideEffects)->toBe([2, 4, 6]);
        });

        it('maintains lazy evaluation', function () {
            $gen = function () {
                yield 1;
                yield 2;
                yield 3;
            };

            $result = new FluentAdapterResult($gen());
            $sideEffects = [];

            $chained = $result->each(function ($item) use (&$sideEffects) {
                $sideEffects[] = "saw:$item";
            });

            expect($sideEffects)->toBe([]);

            iterator_to_array($chained);
            expect($sideEffects)->toBe(['saw:1', 'saw:2', 'saw:3']);
        });

        it('can be chained with other operations', function () {
            $items = [1, 2, 3, 4];
            $result = new FluentAdapterResult($items);

            $sideEffects = [];
            $final = $result
                ->each(function ($item) use (&$sideEffects) {
                    $sideEffects[] = $item;
                })
                ->filter(fn ($x) => $x > 2)
                ->map(fn ($x) => $x * 10)
                ->toArray();

            expect($final)->toBe([2 => 30, 3 => 40])
                ->and($sideEffects)->toBe([1, 2, 3, 4]);
        });
    });

    describe('take', function () {
        it('takes the first N items', function () {
            $items = [1, 2, 3, 4, 5];
            $result = new FluentAdapterResult($items);

            $taken = $result->take(3)->toArray();

            expect($taken)->toBe([1, 2, 3]);
        });

        it('returns all items if limit is greater than count', function () {
            $items = [1, 2, 3];
            $result = new FluentAdapterResult($items);

            $taken = $result->take(10)->toArray();

            expect($taken)->toBe([1, 2, 3]);
        });

        it('returns empty array when taking 0 items', function () {
            $items = [1, 2, 3];
            $result = new FluentAdapterResult($items);

            $taken = $result->take(0)->toArray();

            expect($taken)->toBe([]);
        });

        it('throws exception for negative limit', function () {
            $items = [1, 2, 3];
            $result = new FluentAdapterResult($items);

            expect(fn () => $result->take(-1))->toThrow(InvalidArgumentException::class);
        });

        it('maintains lazy evaluation', function () {
            $gen = function () {
                yield 1;
                yield 2;
                yield 3;
            };

            $result = new FluentAdapterResult($gen());
            $taken = $result->take(2);

            expect(iterator_to_array($taken))->toBe([1, 2]);
        });
    });

    describe('skip', function () {
        it('skips the first N items', function () {
            $items = [1, 2, 3, 4, 5];
            $result = new FluentAdapterResult($items);

            $skipped = $result->skip(2)->toArray();

            expect($skipped)->toBe([2 => 3, 3 => 4, 4 => 5]);
        });

        it('returns empty when skipping more than count', function () {
            $items = [1, 2, 3];
            $result = new FluentAdapterResult($items);

            $skipped = $result->skip(10)->toArray();

            expect($skipped)->toBe([]);
        });

        it('returns all items when skipping 0', function () {
            $items = [1, 2, 3];
            $result = new FluentAdapterResult($items);

            $skipped = $result->skip(0)->toArray();

            expect($skipped)->toBe([1, 2, 3]);
        });

        it('throws exception for negative count', function () {
            $items = [1, 2, 3];
            $result = new FluentAdapterResult($items);

            expect(fn () => $result->skip(-1))->toThrow(InvalidArgumentException::class);
        });

        it('can be combined with take for pagination', function () {
            $items = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
            $result = new FluentAdapterResult($items);

            $page2 = $result->skip(3)->take(3)->toArray();

            expect($page2)->toBe([3 => 4, 4 => 5, 5 => 6]);
        });
    });

    describe('first', function () {
        it('returns the first item', function () {
            $items = [1, 2, 3];
            $result = new FluentAdapterResult($items);

            expect($result->first())->toBe(1);
        });

        it('returns first item matching callback', function () {
            $items = [1, 2, 3, 4, 5];
            $result = new FluentAdapterResult($items);

            expect($result->first(fn ($x) => $x > 3))->toBe(4);
        });

        it('throws exception when no items exist', function () {
            $items = [];
            $result = new FluentAdapterResult($items);

            expect(fn () => $result->first())->toThrow(OutOfBoundsException::class);
        });

        it('throws exception when no matching item found', function () {
            $items = [1, 2, 3];
            $result = new FluentAdapterResult($items);

            expect(fn () => $result->first(fn ($x) => $x > 10))->toThrow(OutOfBoundsException::class);
        });
    });

    describe('last', function () {
        it('returns the last item from array', function () {
            $items = [1, 2, 3];
            $result = new FluentAdapterResult($items);

            expect($result->last())->toBe(3);
        });

        it('returns last item matching callback from array', function () {
            $items = [1, 2, 3, 4, 5];
            $result = new FluentAdapterResult($items);

            expect($result->last(fn ($x) => $x < 4))->toBe(3);
        });

        it('returns the last item from generator', function () {
            $gen = function () {
                yield 1;
                yield 2;
                yield 3;
            };
            $result = new FluentAdapterResult($gen());

            expect($result->last())->toBe(3);
        });

        it('throws exception when no items exist in array', function () {
            $items = [];
            $result = new FluentAdapterResult($items);

            expect(fn () => $result->last())->toThrow(OutOfBoundsException::class);
        });

        it('throws exception when no matching item found in array', function () {
            $items = [1, 2, 3];
            $result = new FluentAdapterResult($items);

            expect(fn () => $result->last(fn ($x) => $x > 10))->toThrow(OutOfBoundsException::class);
        });

        it('throws exception when no items exist in generator', function () {
            $gen = function () {
                yield from [];
            };
            $result = new FluentAdapterResult($gen());

            expect(fn () => $result->last())->toThrow(OutOfBoundsException::class);
        });
    });

    describe('toArray', function () {
        it('converts items to array', function () {
            $items = [1, 2, 3];
            $result = new FluentAdapterResult($items);

            expect($result->toArray())->toBe([1, 2, 3]);
        });

        it('converts generator to array', function () {
            $gen = function () {
                yield 'a' => 1;
                yield 'b' => 2;
            };
            $result = new FluentAdapterResult($gen());

            expect($result->toArray())->toBe(['a' => 1, 'b' => 2]);
        });

        it('returns the same array if already an array', function () {
            $items = [1, 2, 3];
            $result = new FluentAdapterResult($items);

            $array = $result->toArray();
            expect($array)->toBe($items);
        });
    });

    describe('chunk', function () {
        it('chunks items into specified size', function () {
            $items = [1, 2, 3, 4, 5];
            $result = new FluentAdapterResult($items);

            $chunks = $result->chunk(2)->toArray();

            expect($chunks)->toHaveCount(3)
                ->and($chunks[0])->toBe([0 => 1, 1 => 2])
                ->and($chunks[1])->toBe([2 => 3, 3 => 4])
                ->and($chunks[2])->toBe([4 => 5]);
        });

        it('handles exact division', function () {
            $items = [1, 2, 3, 4];
            $result = new FluentAdapterResult($items);

            $chunks = $result->chunk(2)->toArray();

            expect($chunks)->toHaveCount(2)
                ->and($chunks[0])->toBe([0 => 1, 1 => 2])
                ->and($chunks[1])->toBe([2 => 3, 3 => 4]);
        });

        it('throws exception for size less than 1', function () {
            $items = [1, 2, 3];
            $result = new FluentAdapterResult($items);

            expect(fn () => $result->chunk(0))->toThrow(InvalidArgumentException::class)
                ->and(fn () => $result->chunk(-1))->toThrow(InvalidArgumentException::class);
        });

        it('preserves keys in chunks', function () {
            $items = ['a' => 1, 'b' => 2, 'c' => 3];
            $result = new FluentAdapterResult($items);

            $chunks = $result->chunk(2)->toArray();

            expect($chunks[0])->toBe(['a' => 1, 'b' => 2])
                ->and($chunks[1])->toBe(['c' => 3]);
        });
    });

    describe('sort', function () {
        it('sorts items using callback', function () {
            $items = [3, 1, 4, 1, 5, 9, 2, 6];
            $result = new FluentAdapterResult($items);

            $sorted = $result->sort(fn ($a, $b) => $a <=> $b)->toArray();

            expect($sorted)->toBe([1 => 1, 3 => 1, 6 => 2, 0 => 3, 2 => 4, 4 => 5, 7 => 6, 5 => 9]);
        });

        it('sorts in descending order', function () {
            $items = [1, 2, 3, 4, 5];
            $result = new FluentAdapterResult($items);

            $sorted = $result->sort(fn ($a, $b) => $b <=> $a)->toArray();

            expect($sorted)->toBe([4 => 5, 3 => 4, 2 => 3, 1 => 2, 0 => 1]);
        });

        it('preserves keys', function () {
            $items = ['z' => 26, 'a' => 1, 'm' => 13];
            $result = new FluentAdapterResult($items);

            $sorted = $result->sort(fn ($a, $b) => $a <=> $b)->toArray();

            expect($sorted)->toBe(['a' => 1, 'm' => 13, 'z' => 26]);
        });

        it('sorts complex objects', function () {
            $items = [
                ['name' => 'John', 'age' => 30],
                ['name' => 'Jane', 'age' => 25],
                ['name' => 'Bob', 'age' => 35],
            ];
            $result = new FluentAdapterResult($items);

            $sorted = $result->sort(fn ($a, $b) => $a['age'] <=> $b['age'])->toArray();

            expect($sorted[1]['age'])->toBe(25)
                ->and($sorted[0]['age'])->toBe(30)
                ->and($sorted[2]['age'])->toBe(35);
        });
    });

    describe('unique', function () {
        it('filters duplicate values', function () {
            $items = [1, 2, 2, 3, 1, 4, 3, 5];
            $result = new FluentAdapterResult($items);

            $unique = $result->unique()->toArray();

            expect($unique)->toBe([0 => 1, 1 => 2, 3 => 3, 5 => 4, 7 => 5]);
        });

        it('uses callback to determine uniqueness', function () {
            $items = [
                ['id' => 1, 'name' => 'John'],
                ['id' => 2, 'name' => 'Jane'],
                ['id' => 1, 'name' => 'John Doe'],
            ];
            $result = new FluentAdapterResult($items);

            $unique = $result->unique(fn ($item) => $item['id'])->toArray();

            expect($unique)->toHaveCount(2)
                ->and($unique[0]['id'])->toBe(1)
                ->and($unique[1]['id'])->toBe(2);
        });

        it('handles null values', function () {
            $items = [1, null, 2, null, 3];
            $result = new FluentAdapterResult($items);

            $unique = $result->unique()->toArray();

            expect($unique)->toBe([0 => 1, 1 => null, 2 => 2, 4 => 3]);
        });

        it('handles string values', function () {
            $items = ['a', 'b', 'a', 'c', 'b'];
            $result = new FluentAdapterResult($items);

            $unique = $result->unique()->toArray();

            expect($unique)->toBe([0 => 'a', 1 => 'b', 3 => 'c']);
        });

        it('handles array values', function () {
            $items = [
                ['x' => 1],
                ['x' => 2],
                ['x' => 1],
            ];
            $result = new FluentAdapterResult($items);

            $unique = $result->unique()->toArray();

            expect($unique)->toHaveCount(2);
        });
    });

    describe('method chaining', function () {
        it('supports complex method chains', function () {
            $items = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
            $result = new FluentAdapterResult($items);

            $final = $result
                ->filter(fn ($x) => $x % 2 === 0)
                ->map(fn ($x) => $x * 10)
                ->skip(1)
                ->take(2)
                ->toArray();

            expect($final)->toBe([3 => 40, 5 => 60]);
        });

        it('maintains lazy evaluation through multiple operations', function () {
            $callCount = 0;
            $gen = function () use (&$callCount) {
                for ($i = 1; $i <= 100; $i++) {
                    $callCount++;
                    yield $i;
                }
            };

            $result = new FluentAdapterResult($gen());
            $final = $result
                ->filter(fn ($x) => $x % 2 === 0)
                ->map(fn ($x) => $x * 10)
                ->take(3)
                ->toArray();

            expect($callCount)->toBeLessThan(100)
                ->and($final)->toBe([1 => 20, 3 => 40, 5 => 60]);
        });
    });
});
