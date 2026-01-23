<?php

declare(strict_types=1);

use Akeneo\Pim\ApiClient\Pagination\ResourceCursorInterface;

/**
 * Create a mock ResourceCursor for testing adapter classes
 */
function resourceCursorMock(array $items): (Mockery\LegacyMockInterface&Mockery\MockInterface)|ResourceCursorInterface
{
    $mock = mock(ResourceCursorInterface::class);

    $mock->shouldReceive('rewind')->andReturnUsing(function () use (&$pos) {
        $pos = 0;
    });
    $mock->shouldReceive('current')->andReturnUsing(function () use ($items, &$pos) {
        return $items[$pos] ?? null;
    });
    $mock->shouldReceive('key')->andReturnUsing(function () use (&$pos) {
        return $pos;
    });
    $mock->shouldReceive('next')->andReturnUsing(function () use (&$pos) {
        $pos++;
    });
    $mock->shouldReceive('valid')->andReturnUsing(function () use ($items, &$pos) {
        return isset($items[$pos]);
    });
    $mock->shouldReceive('getIterator')->andReturn(new ArrayIterator($items));

    return $mock;
}
