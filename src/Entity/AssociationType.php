<?php

declare(strict_types=1);

namespace AkeneoLib\Entity;

class AssociationType
{
    private ?array $labels;

    private ?bool $isTwoWay;

    private ?bool $isQuantified;

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

    public function isTwoWay(): ?bool
    {
        return $this->isTwoWay ?? null;
    }

    public function setIsTwoWay(?bool $isTwoWay): self
    {
        $this->isTwoWay = $isTwoWay;

        return $this;
    }

    public function isQuantified(): ?bool
    {
        return $this->isQuantified ?? null;
    }

    public function setIsQuantified(?bool $isQuantified): self
    {
        $this->isQuantified = $isQuantified;

        return $this;
    }
}
