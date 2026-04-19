<?php

declare(strict_types=1);

arch('serializer class implements interface')
    ->expect('AkeneoLib\Serializer\Serializer')
    ->toBeClasses()
    ->toImplement('AkeneoLib\Serializer\SerializerInterface');

arch('serializer uses strict types')
    ->expect('AkeneoLib\Serializer')
    ->toUseStrictTypes();

arch('serializer does not depend on adapters')
    ->expect('AkeneoLib\Serializer')
    ->not->toUse('AkeneoLib\Adapter');

arch('serializer does not depend on API client')
    ->expect('AkeneoLib\Serializer')
    ->not->toUse('Akeneo\Pim\ApiClient');
