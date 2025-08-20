<?php

declare(strict_types=1);

namespace AkeneoLib\Adapter;

use Akeneo\Pim\ApiClient\Api\LocaleApiInterface;
use AkeneoLib\Entity\Locale;
use AkeneoLib\Search\QueryParameter;
use AkeneoLib\Serializer\SerializerInterface;
use Generator;

class LocaleAdapter implements LocaleAdapterInterface
{
    public function __construct(
        private readonly LocaleApiInterface $localeApi,
        private readonly SerializerInterface $serializer
    ) {}

    /**
     * {@inheritDoc}
     */
    public function all(?QueryParameter $queryParameters = null): Generator
    {
        $queryParameters ??= new QueryParameter;
        foreach ($this->localeApi->all(100, $queryParameters->toArray()) as $locale) {
            yield $this->serializer->denormalize($locale, Locale::class);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function get(string $code): Locale
    {
        $locale = $this->localeApi->get($code);

        return $this->serializer->denormalize($locale, Locale::class);
    }
}
