<?php

declare(strict_types=1);

namespace AkeneoLib\Entity;

class Attribute
{
    private string $type;

    private bool $scopable;

    private bool $localizable;

    private ?string $defaultMetricUnit;

    public function __construct(private string $code) {}

    public function getType(): ?string
    {
        return $this->type ?? null;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function isScopable(): ?bool
    {
        return $this->scopable ?? null;
    }

    public function setScopable(bool $scopable): self
    {
        $this->scopable = $scopable;

        return $this;
    }

    public function isLocalizable(): ?bool
    {
        return $this->localizable ?? null;
    }

    public function setLocalizable(bool $localizable): self
    {
        $this->localizable = $localizable;

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

    public function getDefaultMetricUnit(): ?string
    {
        return $this->defaultMetricUnit ?? null;
    }

    public function setDefaultMetricUnit(?string $defaultMetricUnit): self
    {
        $this->defaultMetricUnit = $defaultMetricUnit;

        return $this;
    }
}
