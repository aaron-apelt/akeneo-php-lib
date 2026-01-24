<?php

declare(strict_types=1);

namespace AkeneoLib\Entity;

class Family
{
    private ?array $labels;

    private ?array $attributes;

    private ?string $attributeAsLabel;

    private ?string $attributeAsImage;

    private ?string $attributeAsMainMedia;

    private ?array $attributeRequirements;

    private ?string $parent;

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

    public function getAttributes(): ?array
    {
        return $this->attributes ?? null;
    }

    public function setAttributes(?array $attributes): self
    {
        $this->attributes = $attributes;

        return $this;
    }

    public function getAttributeAsLabel(): ?string
    {
        return $this->attributeAsLabel ?? null;
    }

    public function setAttributeAsLabel(?string $attributeAsLabel): self
    {
        $this->attributeAsLabel = $attributeAsLabel;

        return $this;
    }

    public function getAttributeAsImage(): ?string
    {
        return $this->attributeAsImage ?? null;
    }

    public function setAttributeAsImage(?string $attributeAsImage): self
    {
        $this->attributeAsImage = $attributeAsImage;

        return $this;
    }

    public function getAttributeAsMainMedia(): ?string
    {
        return $this->attributeAsMainMedia ?? null;
    }

    public function setAttributeAsMainMedia(?string $attributeAsMainMedia): self
    {
        $this->attributeAsMainMedia = $attributeAsMainMedia;

        return $this;
    }

    public function getAttributeRequirements(): ?array
    {
        return $this->attributeRequirements ?? null;
    }

    public function setAttributeRequirements(?array $attributeRequirements): self
    {
        $this->attributeRequirements = $attributeRequirements;

        return $this;
    }

    public function getParent(): ?string
    {
        return $this->parent ?? null;
    }

    public function setParent(?string $parent): self
    {
        $this->parent = $parent;

        return $this;
    }
}
