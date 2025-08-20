<?php

declare(strict_types=1);

namespace AkeneoLib\Adapter;

use Akeneo\Pim\ApiClient\Exception\HttpException;
use AkeneoLib\Entity\Locale;
use AkeneoLib\Exception\SerializationException;
use AkeneoLib\Search\QueryParameter;
use Generator;

interface LocaleAdapterInterface
{
    /**
     * Receives all locales for the given queryParameters and return them denormalized inside a Generator.
     *
     * @return Generator<Locale>
     *
     * @throws SerializationException
     */
    public function all(?QueryParameter $queryParameters = null): Generator;

    /**
     * Receives a locale by code and denormalize it to a Locale object.
     *
     * @throws HttpException
     * @throws SerializationException
     */
    public function get(string $code): Locale;
}
