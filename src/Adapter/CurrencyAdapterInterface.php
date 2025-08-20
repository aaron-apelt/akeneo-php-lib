<?php

declare(strict_types=1);

namespace AkeneoLib\Adapter;

use Akeneo\Pim\ApiClient\Exception\HttpException;
use AkeneoLib\Entity\Currency;
use AkeneoLib\Exception\SerializationException;
use AkeneoLib\Search\QueryParameter;
use Generator;

interface CurrencyAdapterInterface
{
    /**
     * Receives all currencies for the given queryParameters and return them denormalized inside a Generator.
     *
     * @return Generator<Currency>
     *
     * @throws SerializationException
     */
    public function all(?QueryParameter $queryParameters = null): Generator;

    /**
     * Receives a currency by code and denormalize it to a Currency object.
     *
     * @throws HttpException
     * @throws SerializationException
     */
    public function get(string $code): Currency;
}
