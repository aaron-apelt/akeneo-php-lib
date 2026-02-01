<?php

declare(strict_types=1);

arch('no circular dependencies between main namespaces')
    ->expect('AkeneoLib\Entity')
    ->not->toUse('AkeneoLib\Adapter');

arch('adapters can use entities')
    ->expect('AkeneoLib\Adapter')
    ->toUse('AkeneoLib\Entity');

arch('adapters can use serializer')
    ->expect('AkeneoLib\Adapter')
    ->toUse('AkeneoLib\Serializer');

arch('serializer can use entities')
    ->expect('AkeneoLib\Serializer')
    ->toUse('AkeneoLib\Entity');

arch('no debug functions in production code')
    ->expect('AkeneoLib')
    ->not->toUse(['dd', 'dump', 'var_dump', 'print_r', 'var_export']);

arch('no sleep functions in production code')
    ->expect('AkeneoLib')
    ->not->toUse(['sleep', 'usleep']);

arch('no eval in production code')
    ->expect('AkeneoLib')
    ->not->toUse('eval');

arch('no exit or die in production code')
    ->expect('AkeneoLib')
    ->not->toUse(['exit', 'die']);
