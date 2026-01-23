<?php

declare(strict_types=1);

namespace AkeneoLib\Adapter\Support;

interface FluentAdapterResultInterface
{
    public function __construct(iterable $items);
}
