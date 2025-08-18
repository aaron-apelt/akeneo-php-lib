<?php

declare(strict_types=1);

namespace AkeneoLib\Serializer;

use AkeneoLib\Exception\SerializationException;
use AkeneoLib\Serializer\Normalizer\ValuesDenormalizer;
use AkeneoLib\Serializer\Normalizer\ValuesNormalizer;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer as BaseSerializer;
use Throwable;

class Serializer implements SerializerInterface
{
    private BaseSerializer $serializer;

    public function __construct(?BaseSerializer $serializer = null)
    {
        if ($serializer) {
            $this->serializer = $serializer;
        } else {
            $normalizers = [
                new ValuesDenormalizer,
                new ValuesNormalizer,
                new ArrayDenormalizer,
                new ObjectNormalizer(
                    null,
                    new CamelCaseToSnakeCaseNameConverter,
                    null,
                    new ReflectionExtractor
                ),
            ];

            $this->serializer = new BaseSerializer($normalizers);
        }
    }

    public function denormalize(array $data, string $type, array $context = []): mixed
    {
        try {
            return $this->serializer->denormalize($data, $type, context: $context);
        } catch (Throwable $e) {
            throw new SerializationException(
                sprintf('Failed to denormalize data to class "%s": %s', $type, $e->getMessage()),
                previous: $e
            );
        }
    }

    public function normalize(array|object $data, array $context = []): array
    {
        try {
            return $this->serializer->normalize($data, context: $context);
        } catch (Throwable $e) {
            throw new SerializationException(
                sprintf('Failed to normalize data: %s', $e->getMessage()),
                previous: $e
            );
        }
    }
}
