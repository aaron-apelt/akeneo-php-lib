<?php

declare(strict_types=1);

namespace AkeneoLib\Entity;

class Currency
{
    private bool $enabled;

    private string $label;

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

    public function isEnabled(): ?bool
    {
        return $this->enabled ?? null;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getLabel(): ?string
    {
        return $this->label ?? null;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

        return $this;
    }
}
