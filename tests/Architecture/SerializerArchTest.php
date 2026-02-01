<?php

declare(strict_types=1);

arch('serializer classes are in Serializer namespace')
    ->expect('AkeneoLib\Serializer\Serializer')
    ->toBeClasses()
    ->toImplement('AkeneoLib\Serializer\SerializerInterface');

arch('serializer uses strict types')
    ->expect('AkeneoLib\Serializer\Serializer')
    ->toUseStrictTypes();

arch('normalizers follow naming conventions')
    ->expect('AkeneoLib\Serializer\Normalizer\ValuesNormalizer')
    ->toHaveSuffix('Normalizer');

arch('denormalizers follow naming conventions')
    ->expect('AkeneoLib\Serializer\Normalizer\ValuesDenormalizer')
    ->toHaveSuffix('Denormalizer');

arch('serializer does not depend on adapters')
    ->expect('AkeneoLib\Serializer\Serializer')
    ->not->toUse('AkeneoLib\Adapter');

arch('serializer does not depend on API client')
    ->expect('AkeneoLib\Serializer\Serializer')
    ->not->toUse('Akeneo\Pim\ApiClient');
