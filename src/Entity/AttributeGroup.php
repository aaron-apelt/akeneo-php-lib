<?php

declare(strict_types=1);

namespace AkeneoLib\Entity;

class AttributeGroup
{
    private ?array $labels;

    private ?int $sortOrder;

    private ?array $attributes;

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

    public function setLabels(?array $labels): self
    {
        $this->labels = $labels;

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

    public function getAttributes(): ?array
    {
        return $this->attributes ?? null;
    }

    public function setAttributes(?array $attributes): self
    {
        $this->attributes = $attributes;

        return $this;
    }
}
