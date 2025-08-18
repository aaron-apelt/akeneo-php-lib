<?php

declare(strict_types=1);

namespace AkeneoLib\Serializer\Normalizer;

use AkeneoLib\Entity\Values;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ValuesNormalizer implements NormalizerInterface
{
    public function normalize(mixed $data, ?string $format = null, array $context = []): array
    {
        $scopeName = $context['scopeName'] ?? 'scope';

        $normalized = [];
        foreach ($data as $value) {
            $normalized[$value->getAttributeCode()][] = [
                'locale' => $value->getLocale(),
                $scopeName => $value->getScope(),
                'data' => $value->getData(),
            ];
        }

        return $normalized;
    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Values::class => false,
        ];
    }

    public function supportsNormalization(mixed $data, ?string $format = null, array $context = []): bool
    {
        return $data instanceof Values;
    }
}
