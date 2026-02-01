<?php

declare(strict_types=1);

namespace AkeneoLib\Entity;

class AttributeOption
{
    private array $labels;

    private ?string $attribute;

    private ?int $sortOrder;

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

    public function getLabels(): ?array
    {
        return $this->labels ?? null;
    }

    public function setLabels(array $labels): self
    {
        $this->labels = $labels;

        return $this;
    }

    public function getAttribute(): ?string
    {
        return $this->attribute ?? null;
    }

    public function setAttribute(?string $attribute): self
    {
        $this->attribute = $attribute;

        return $this;
    }

    public function getSortOrder(): ?int
    {
        return $this->sortOrder ?? null;
    }

    public function setSortOrder(?int $sortOrder): self
    {
        $this->sortOrder = $sortOrder;

        return $this;
    }
}
