<?php

declare(strict_types=1);

namespace AkeneoLib\Entity;

class Channel
{
    private ?array $labels;

    private ?array $currencies;

    private ?array $locales;

    private ?string $categoryTree;

    private ?array $conversionUnits;

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

    public function getCurrencies(): ?array
    {
        return $this->currencies ?? null;
    }

    public function setCurrencies(?array $currencies): self
    {
        $this->currencies = $currencies;

        return $this;
    }

    public function getLocales(): ?array
    {
        return $this->locales ?? null;
    }

    public function setLocales(?array $locales): self
    {
        $this->locales = $locales;

        return $this;
    }

    public function getCategoryTree(): ?string
    {
        return $this->categoryTree ?? null;
    }

    public function setCategoryTree(?string $categoryTree): self
    {
        $this->categoryTree = $categoryTree;

        return $this;
    }

    public function getConversionUnits(): ?array
    {
        return $this->conversionUnits ?? null;
    }

    public function setConversionUnits(?array $conversionUnits): self
    {
        $this->conversionUnits = $conversionUnits;

        return $this;
    }
}
