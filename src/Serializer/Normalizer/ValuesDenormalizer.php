<?php

declare(strict_types=1);

namespace AkeneoLib\Serializer\Normalizer;

use AkeneoLib\Entity\Value;
use AkeneoLib\Entity\Values;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

use function is_string;

class ValuesDenormalizer implements DenormalizerInterface
{
    private string $scopeName = 'scope';

    public function denormalize(mixed $data, string $type, ?string $format = null, array $context = []): Values
    {
        $this->scopeName = $context['scopeName'] ?? 'scope';

        $valuesObject = new Values;
        foreach ($data as $attributeCode => $values) {
            foreach ($values as $value) {
                $valuesObject->upsert(new Value($attributeCode, $value['data'], $value[$this->scopeName], $value['locale']));
            }
        }

        return $valuesObject;
    }

    public function supportsDenormalization(mixed $data, string $type, ?string $format = null, array $context = []): bool
    {
        if ($type !== Values::class || ! is_array($data)) {
            return false;
        }

        return array_all($data, fn ($valueArray, $attributeCode) => $this->isAttributeCode($attributeCode) || $this->isValueArray($valueArray));

    }

    public function getSupportedTypes(?string $format): array
    {
        return [
            Values::class => false,
        ];
    }

    private function isAttributeCode(mixed $attributeCode): bool
    {
        return is_string($attributeCode) && $attributeCode !== '';
    }

    private function isValueArray(mixed $valueArray): bool
    {
        if (! is_array($valueArray)) {
            return false;
        }

        return array_all($valueArray, fn ($dataArray) => $this->isDataArray($dataArray));

    }

    private function isDataArray(mixed $dataArray): bool
    {
        if (! is_array($dataArray)) {
            return false;
        }

        return array_key_exists('locale', $dataArray)
            && array_key_exists($this->scopeName, $dataArray)
            && array_key_exists('data', $dataArray);
    }
}
