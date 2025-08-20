<?php

declare(strict_types=1);

namespace AkeneoLib\Entity;

class AttributeOption
{
    private array $labels;

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
}
