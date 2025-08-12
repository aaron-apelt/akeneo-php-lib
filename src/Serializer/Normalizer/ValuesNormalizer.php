<?php

namespace AkeneoLib\Serializer\Normalizer;

use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ValuesNormalizer implements NormalizerInterface
{
    public function normalize(mixed $object, string $format = null, array $context = []): array
    {
        if (!is_array($object)) {
            return [];
        }

        $normalized = [];
        foreach ($object as $attributeCode => $values) {
            $normalized[$attributeCode] = [];
            foreach ($values as $value) {
                $normalized[$attributeCode][] = [
                    'locale' => $value['locale'] ?? null,
                    'scope' => $value['scope'] ?? null,
                    'data' => $value['data'] ?? null,
                ];
            }
        }

        return $normalized;
    }

    public function supportsNormalization(mixed $data, string $format = null): bool
    {
        return is_array($data) && !empty($data) && is_array(reset($data));
    }
}