<?php

declare(strict_types=1);

arch('adapter interfaces follow naming convention')
    ->expect('AkeneoLib\Adapter')
    ->interfaces()
    ->toHaveSuffix('AdapterInterface');

arch('adapter classes follow naming convention')
    ->expect('AkeneoLib\Adapter')
    ->classes()
    ->toHaveSuffix('Adapter')
    ->ignoring('AkeneoLib\Adapter\Support');

arch('adapters use strict types')
    ->expect('AkeneoLib\Adapter')
    ->toUseStrictTypes();
