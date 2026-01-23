<?php

declare(strict_types=1);

namespace AkeneoLib\Adapter;

use Akeneo\Pim\ApiClient\Api\CurrencyApiInterface;
use AkeneoLib\Adapter\Support\FluentAdapterResult;
use AkeneoLib\Entity\Currency;
use AkeneoLib\Search\QueryParameter;
use AkeneoLib\Serializer\SerializerInterface;
use Generator;

class CurrencyAdapter implements CurrencyAdapterInterface
{
    public function __construct(
        private readonly CurrencyApiInterface $currencyApi,
        private readonly SerializerInterface $serializer
    ) {}

    /**
     * {@inheritDoc}
     */
    public function all(?QueryParameter $queryParameters = null): FluentAdapterResult
    {
        $queryParameters ??= new QueryParameter;
        $generator = function () use ($queryParameters): Generator {
            foreach ($this->currencyApi->all(100, $queryParameters->toArray()) as $currency) {
                yield $this->serializer->denormalize($currency, Currency::class);
            }
        };

        return new FluentAdapterResult($generator());
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $code): Currency
    {
        $currency = $this->currencyApi->get($code);

        return $this->serializer->denormalize($currency, Currency::class);
    }
}
