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

arch('adapter implementations use serializer interface')
    ->expect('AkeneoLib\Adapter\ProductAdapter')
    ->toUse('AkeneoLib\Serializer\SerializerInterface');

arch('adapter implementations use API client')
    ->expect('AkeneoLib\Adapter\ProductAdapter')
    ->toUse('Akeneo\Pim\ApiClient');

arch('adapter interfaces are used only in Adapter namespace')
    ->expect('AkeneoLib\Adapter')
    ->interfaces()
    ->toOnlyBeUsedIn('AkeneoLib\Adapter');
