<?php

declare(strict_types=1);

namespace AkeneoLib\Entity;

class ReferenceEntity
{
    private ?array $labels;

    private ?string $image;

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
        return $this->labels;
    }

    public function setLabels(?array $labels): self
    {
        $this->labels = $labels;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image ?? null;
    }

    public function setImage(?string $image): self
    {
        $this->image = $image;

        return $this;
    }
}
