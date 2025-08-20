<?php

declare(strict_types=1);

namespace AkeneoLib\Entity;

class Locale
{
    private bool $enabled;

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
}
