<?php

declare(strict_types=1);

arch('entities are in the Entity namespace')
    ->expect('AkeneoLib\Entity')
    ->classes()
    ->not->toBeAbstract()
    ->ignoring('AkeneoLib\Entity\ValuesTrait');

arch('entities have consistent naming')
    ->expect('AkeneoLib\Entity')
    ->classes()
    ->not->toHaveSuffix('Entity')
    ->ignoring([
        'AkeneoLib\Entity\ReferenceEntity',
        'AkeneoLib\Entity\ReferenceEntityRecord',
    ]);

arch('entities are final or extendable only where needed')
    ->expect('AkeneoLib\Entity\Values')
    ->toBeClasses()
    ->toBeFinal();

arch('entities do not use static methods')
    ->expect('AkeneoLib\Entity')
    ->classes()
    ->not->toHaveMethod('__callStatic');

arch('entities have proper constructors')
    ->expect('AkeneoLib\Entity')
    ->classes()
    ->toHaveConstructor()
    ->ignoring([
        'AkeneoLib\Entity\Values',
        'AkeneoLib\Entity\Value',
        'AkeneoLib\Entity\ValuesTrait',
    ]);

arch('entity classes use strict types')
    ->expect('AkeneoLib\Entity')
    ->toUseStrictTypes();

arch('entities do not depend on adapters')
    ->expect('AkeneoLib\Entity')
    ->not->toUse('AkeneoLib\Adapter');

arch('entities do not depend on API client directly')
    ->expect('AkeneoLib\Entity')
    ->not->toUse('Akeneo\Pim\ApiClient');

arch('entities only depend on allowed namespaces')
    ->expect('AkeneoLib\Entity')
    ->toOnlyUse([
        'AkeneoLib\Entity',
        'Generator',
        'IteratorAggregate',
    ]);
