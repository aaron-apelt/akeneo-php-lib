<?php

declare(strict_types=1);

namespace AkeneoLib\Entity;

class ReferenceEntity
{
    private ?string $code;
    private ?array $labels;

    public function __construct(string $code, ?array $labels = null)
    {
        $this->code = $code;
        $this->labels = $labels;
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
        return $this->labels;
    }

    public function setLabels(?array $labels): self
    {
        $this->labels = $labels;
        return $this;
    }
}