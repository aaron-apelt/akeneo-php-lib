<?php

declare(strict_types=1);

namespace AkeneoLib\Entity;

class Category
{
    private string $code;

    private ?string $parent;

    private ?array $labels;

    private ?int $position;

    private ?array $channelRequirements;

    public function __construct(string $code, ?string $parent = null, ?array $labels = null)
    {
        $this->code = $code;
        $this->parent = $parent;
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

    public function getParent(): ?string
    {
        return $this->parent;
    }

    public function setParent(?string $parent): self
    {
        $this->parent = $parent;

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

    public function getPosition(): ?int
    {
        return $this->position ?? null;
    }

    public function setPosition(?int $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getChannelRequirements(): ?array
    {
        return $this->channelRequirements ?? null;
    }

    public function setChannelRequirements(?array $channelRequirements): self
    {
        $this->channelRequirements = $channelRequirements;

        return $this;
    }
}
