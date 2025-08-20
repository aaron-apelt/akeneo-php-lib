<?php

declare(strict_types=1);

namespace AkeneoLib\Serializer;

use AkeneoLib\Exception\SerializationException;

interface SerializerInterface
{
    /**
     * Tries de-normalizing the given array into the provided class.
     *
     * @throws SerializationException
     */
    public function denormalize(array $data, string $type, array $context = []): mixed;

    /**
     * Normalizes the given object into an array.
     *
     * @throws SerializationException
     */
    public function normalize(array|object $data, array $context = []): array;
}
