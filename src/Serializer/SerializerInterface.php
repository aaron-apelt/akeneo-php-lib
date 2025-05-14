<?php

declare(strict_types=1);

namespace AkeneoLib\Serializer;

use AkeneoLib\Exception\SerializationException;

interface SerializerInterface
{
    /**
     * Trys de-normalizing the given array into the provided class.
     */
    public function denormalize(array $data, string $format): mixed;

    /**
     * Normalizes the given object into an array.
     *
     * @throws SerializationException
     */
    public function normalize(object $data): array;
}
