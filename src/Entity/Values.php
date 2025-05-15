<?php

declare(strict_types=1);

namespace AkeneoLib\Entity;

use Generator;
use IteratorAggregate;

final class Values implements IteratorAggregate
{
    private array $values = [];

    /**
     * @return Generator<Value>
     */
    public function getIterator(): Generator
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
        $key = $this->generateKey($value->getAttributeCode(), $value->getScope(), $value->getLocale());
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
        $scope ??= 'null';
        $locale ??= 'null';

        return $attributeCode.'_'.$scope.'_'.$locale;
    }
}
