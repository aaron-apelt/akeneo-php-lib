<?php

declare(strict_types=1);

namespace AkeneoLib\Entity;

class Value
{
    public function __construct(
        protected string $attributeCode,
        protected null|array|bool|float|int|string $data,
        protected ?string $scope = null,
        protected ?string $locale = null
    ) {}

    public function getAttributeCode(): string
    {
        return $this->attributeCode;
    }

    public function setAttributeCode(string $attributeCode): self
    {
        $this->attributeCode = $attributeCode;

        return $this;
    }

    public function getData(): null|array|bool|float|int|string
    {
        return $this->data;
    }

    public function setData(null|array|bool|float|int|string $data): self
    {
        $this->data = $data;

        return $this;
    }

    public function getScope(): ?string
    {
        return $this->scope;
    }

    public function setScope(?string $scope): self
    {
        $this->scope = $scope;

        return $this;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(?string $locale): self
    {
        $this->locale = $locale;

        return $this;
    }
}
