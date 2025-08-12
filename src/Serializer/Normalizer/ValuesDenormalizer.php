<?php

namespace AkeneoLib\Serializer\Normalizer;

use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class ValuesDenormalizer implements DenormalizerInterface
{
    public function denormalize(mixed $data, string $type, string $format = null, array $context = []): array
    {
        $denormalized = [];
        foreach ($data as $attributeCode => $values) {
            $denormalized[$attributeCode] = [];
            foreach ($values as $value) {
                $denormalized[$attributeCode][] = [
                    'locale' => $value['locale'] ?? null,
                    'scope' => $value['scope'] ?? null,
                    'data' => $value['data'] ?? null,
                ];
            }
        }

        return $denormalized;
    }

    public function supportsDenormalization(mixed $data, string $type, string $format = null): bool
    {
        return is_array($data);
    }
}