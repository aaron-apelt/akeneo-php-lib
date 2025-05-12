<?php

declare(strict_types=1);

namespace AkeneoLib\Entity;

use IteratorAggregate;

class Values implements IteratorAggregate
{
    public array $values = [];

    public function getIterator(): \Generator
    {
        yield from $this->values;
    }

    public function get(string $attributeCode, ?string $scope = null, ?string $locale = null): ?Value
    {
        $key = $this->generateKey($attributeCode, $scope, $locale);
        return $this->values[$key] ?? null;
    }

    public function upsert(Value $value): self
    {
        $key                = $this->generateKey($value->attributeCode, $value->scope, $value->locale);
        $this->values[$key] = $value;
        return $this;
    }

    public function remove(string $attributeCode, ?string $scope = null, ?string $locale = null): self
    {
        $key = $this->generateKey($attributeCode, $scope, $locale);
        unset($this->values[$key]);
        return $this;
    }

    private function generateKey(string $attributeCode, ?string $scope = null, ?string $locale = null): string
    {
        $scope  = $scope ?? 'null';
        $locale = $locale ?? 'null';
        return $attributeCode . '_' . $scope . '_' . $locale;
    }
}