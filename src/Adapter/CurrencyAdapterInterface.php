<?php

declare(strict_types=1);

namespace AkeneoLib\Adapter;

use Akeneo\Pim\ApiClient\Exception\HttpException;
use AkeneoLib\Adapter\Support\FluentAdapterResult;
use AkeneoLib\Entity\Currency;
use AkeneoLib\Exception\SerializationException;
use AkeneoLib\Search\QueryParameter;

interface CurrencyAdapterInterface
{
    /**
     * Receives all currencies for the given queryParameters and return them denormalized inside a FluentAdapterResult.
     *
     * @return FluentAdapterResult<Currency>
     *
     * @throws SerializationException
     */
    public function all(?QueryParameter $queryParameters = null): FluentAdapterResult;

    /**
     * Receives a currency by code and denormalize it to a Currency object.
     *
     * @throws HttpException
     * @throws SerializationException
     */
    public function get(string $code): Currency;
}
