<?php

declare(strict_types=1);

namespace AkeneoLib\Entity;

class Product
{
    use ValuesTrait;

    private ?bool $enabled;

    private ?string $family;

    private ?array $categories;

    private ?string $parent;

    public function __construct(private string $identifier) {}

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function setIdentifier(string $identifier): self
    {
        $this->identifier = $identifier;

        return $this;
    }

    public function isEnabled(): ?bool
    {
        return $this->enabled ?? null;
    }

    public function setEnabled(?bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }

    public function getFamily(): ?string
    {
        return $this->family ?? null;
    }

    public function setFamily(?string $family): self
    {
        $this->family = $family;

        return $this;
    }

    public function getCategories(): ?array
    {
        return $this->categories ?? null;
    }

    public function setCategories(?array $categories): self
    {
        $this->categories = $categories;

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
