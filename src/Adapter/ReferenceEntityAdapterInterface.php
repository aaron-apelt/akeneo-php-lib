<?php

declare(strict_types=1);

namespace AkeneoLib\Adapter;

use Akeneo\Pim\ApiClient\Exception\HttpException;
use AkeneoLib\Entity\ReferenceEntity;
use AkeneoLib\Exception\SerializationException;
use AkeneoLib\Search\QueryParameter;
use Generator;

interface ReferenceEntityAdapterInterface
{
    /**
     * Receives all reference entities for the given queryParameters and return them denormalized inside a Generator.
     *
     * @return Generator<ReferenceEntity>
     *
     * @throws SerializationException if the serialization fails
     */
    public function all(?QueryParameter $queryParameters = null): Generator;

    /**
     * Receives a reference entity by a given code and denormalize it to a ReferenceEntity object.
     *
     * @throws HttpException if the request failed
     * @throws SerializationException if the serialization fails
     */
    public function get(string $code): ReferenceEntity;

    /**
     * Upsert the normalized reference entity to Akeneo.
     */
    public function upsert(ReferenceEntity $referenceEntity): void;
}
