<?php

declare(strict_types=1);

namespace AkeneoLib\Entity;

class ReferenceEntityRecord
{
    private ?Values $values;

    public function __construct(private string $code) {}

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getValue(string $code, ?string $scope = null, ?string $locale = null): ?Value
    {
        return $this->getValues()?->get($code, $scope, $locale);
    }

    public function getValues(): ?Values
    {
        return $this->values ?? null;
    }

    public function setValues(?Values $values): self
    {
        $this->values = $values;

        return $this;
    }

    public function upsertValue(Value $value): self
    {
        if (! isset($this->values)) {
            $this->setValues(new Values);
        }
        $this->values->upsert($value);

        return $this;
    }
}
