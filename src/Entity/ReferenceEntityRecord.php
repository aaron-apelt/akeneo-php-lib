<?php

declare(strict_types=1);

namespace AkeneoLib\Entity;

class ReferenceEntityRecord
{
    use ValuesTrait;

    private ?string $created;

    private ?string $updated;

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

    public function getCreated(): ?string
    {
        return $this->created ?? null;
    }

    public function setCreated(?string $created): self
    {
        $this->created = $created;

        return $this;
    }

    public function getUpdated(): ?string
    {
        return $this->updated ?? null;
    }

    public function setUpdated(?string $updated): self
    {
        $this->updated = $updated;

        return $this;
    }
}
