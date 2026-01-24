<?php

declare(strict_types=1);

namespace AkeneoLib\Entity;

class Asset
{
    use ValuesTrait;

    private ?string $assetFamilyCode;

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

    public function getAssetFamilyCode(): ?string
    {
        return $this->assetFamilyCode ?? null;
    }

    public function setAssetFamilyCode(?string $assetFamilyCode): self
    {
        $this->assetFamilyCode = $assetFamilyCode;

        return $this;
    }
}
