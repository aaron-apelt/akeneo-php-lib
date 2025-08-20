<?php

declare(strict_types=1);

namespace AkeneoLib\Entity;

class FamilyVariant
{
    private ?array $labels;

    private ?array $variantAttributeSets;

    public function __construct(private string $family, private string $code) {}

    public function getFamily(): string
    {
        return $this->family;
    }

    public function setFamily(string $family): self
    {
        $this->family = $family;

        return $this;
    }

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

    public function getVariantAttributeSets(): ?array
    {
        return $this->variantAttributeSets ?? null;
    }

    public function setVariantAttributeSets(?array $variantAttributeSets): self
    {
        $this->variantAttributeSets = $variantAttributeSets;

        return $this;
    }
}
