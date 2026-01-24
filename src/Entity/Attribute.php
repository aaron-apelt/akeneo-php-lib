<?php

declare(strict_types=1);

namespace AkeneoLib\Entity;

class Attribute
{
    private string $type;

    private bool $scopable;

    private bool $localizable;

    private ?array $labels;

    private ?string $group;

    private ?bool $unique;

    private ?bool $useableAsGridFilter;

    private ?array $availableLocales;

    private ?string $defaultMetricUnit;

    private ?int $maxCharacters;

    private ?string $validationRule;

    private ?string $validationRegexp;

    private ?bool $wysiwygEnabled;

    private ?int $numberMin;

    private ?int $numberMax;

    private ?bool $decimalsAllowed;

    private ?bool $negativeAllowed;

    private ?string $dateMin;

    private ?string $dateMax;

    private ?string $maxFileSize;

    private ?array $allowedExtensions;

    private ?string $metricFamily;

    private ?string $referenceDataName;

    private ?bool $defaultValue;

    private ?int $sortOrder;

    private ?bool $isMainIdentifier;

    private ?bool $isMandatory;

    private ?string $decimalPlacesStrategy;

    private ?int $decimalPlaces;

    private ?bool $enableOptionCreationDuringImport;

    private ?int $maxItemsCount;

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

    public function getLabels(): ?array
    {
        return $this->labels ?? null;
    }

    public function setLabels(?array $labels): self
    {
        $this->labels = $labels;

        return $this;
    }

    public function getGroup(): ?string
    {
        return $this->group ?? null;
    }

    public function setGroup(?string $group): self
    {
        $this->group = $group;

        return $this;
    }

    public function isUnique(): ?bool
    {
        return $this->unique ?? null;
    }

    public function setUnique(?bool $unique): self
    {
        $this->unique = $unique;

        return $this;
    }

    public function isUseableAsGridFilter(): ?bool
    {
        return $this->useableAsGridFilter ?? null;
    }

    public function setUseableAsGridFilter(?bool $useableAsGridFilter): self
    {
        $this->useableAsGridFilter = $useableAsGridFilter;

        return $this;
    }

    public function getAvailableLocales(): ?array
    {
        return $this->availableLocales ?? null;
    }

    public function setAvailableLocales(?array $availableLocales): self
    {
        $this->availableLocales = $availableLocales;

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

    public function getMaxCharacters(): ?int
    {
        return $this->maxCharacters ?? null;
    }

    public function setMaxCharacters(?int $maxCharacters): self
    {
        $this->maxCharacters = $maxCharacters;

        return $this;
    }

    public function getValidationRule(): ?string
    {
        return $this->validationRule ?? null;
    }

    public function setValidationRule(?string $validationRule): self
    {
        $this->validationRule = $validationRule;

        return $this;
    }

    public function getValidationRegexp(): ?string
    {
        return $this->validationRegexp ?? null;
    }

    public function setValidationRegexp(?string $validationRegexp): self
    {
        $this->validationRegexp = $validationRegexp;

        return $this;
    }

    public function isWysiwygEnabled(): ?bool
    {
        return $this->wysiwygEnabled ?? null;
    }

    public function setWysiwygEnabled(?bool $wysiwygEnabled): self
    {
        $this->wysiwygEnabled = $wysiwygEnabled;

        return $this;
    }

    public function getNumberMin(): ?int
    {
        return $this->numberMin ?? null;
    }

    public function setNumberMin(?int $numberMin): self
    {
        $this->numberMin = $numberMin;

        return $this;
    }

    public function getNumberMax(): ?int
    {
        return $this->numberMax ?? null;
    }

    public function setNumberMax(?int $numberMax): self
    {
        $this->numberMax = $numberMax;

        return $this;
    }

    public function isDecimalsAllowed(): ?bool
    {
        return $this->decimalsAllowed ?? null;
    }

    public function setDecimalsAllowed(?bool $decimalsAllowed): self
    {
        $this->decimalsAllowed = $decimalsAllowed;

        return $this;
    }

    public function isNegativeAllowed(): ?bool
    {
        return $this->negativeAllowed ?? null;
    }

    public function setNegativeAllowed(?bool $negativeAllowed): self
    {
        $this->negativeAllowed = $negativeAllowed;

        return $this;
    }

    public function getDateMin(): ?string
    {
        return $this->dateMin ?? null;
    }

    public function setDateMin(?string $dateMin): self
    {
        $this->dateMin = $dateMin;

        return $this;
    }

    public function getDateMax(): ?string
    {
        return $this->dateMax ?? null;
    }

    public function setDateMax(?string $dateMax): self
    {
        $this->dateMax = $dateMax;

        return $this;
    }

    public function getMaxFileSize(): ?string
    {
        return $this->maxFileSize ?? null;
    }

    public function setMaxFileSize(?string $maxFileSize): self
    {
        $this->maxFileSize = $maxFileSize;

        return $this;
    }

    public function getAllowedExtensions(): ?array
    {
        return $this->allowedExtensions ?? null;
    }

    public function setAllowedExtensions(?array $allowedExtensions): self
    {
        $this->allowedExtensions = $allowedExtensions;

        return $this;
    }

    public function getMetricFamily(): ?string
    {
        return $this->metricFamily ?? null;
    }

    public function setMetricFamily(?string $metricFamily): self
    {
        $this->metricFamily = $metricFamily;

        return $this;
    }

    public function getReferenceDataName(): ?string
    {
        return $this->referenceDataName ?? null;
    }

    public function setReferenceDataName(?string $referenceDataName): self
    {
        $this->referenceDataName = $referenceDataName;

        return $this;
    }

    public function getSortOrder(): ?int
    {
        return $this->sortOrder ?? null;
    }

    public function setSortOrder(?int $sortOrder): self
    {
        $this->sortOrder = $sortOrder;

        return $this;
    }

    public function hasDefaultValue(): bool
    {
        return ($this->defaultValue ?? null) !== null;
    }

    public function setDefaultValue(?bool $hasDefaultValue): self
    {
        $this->defaultValue = $hasDefaultValue;

        return $this;
    }

    public function isMainIdentifier(): ?bool
    {
        return $this->isMainIdentifier ?? null;
    }

    public function setIsMainIdentifer(?bool $isMainIdentifier): self
    {
        $this->isMainIdentifier = $isMainIdentifier;

        return $this;
    }

    public function isMandatory(): ?bool
    {
        return $this->isMandatory ?? null;
    }

    public function setMandatory(?bool $isMandatory): self
    {
        $this->isMandatory = $isMandatory;

        return $this;
    }

    public function getDecimalPlacesStrategy(): ?string
    {
        return $this->decimalPlacesStrategy ?? null;
    }

    public function setDecimalPlacesStrategy(?string $decimalPlacesStrategy): self
    {
        $this->decimalPlacesStrategy = $decimalPlacesStrategy;

        return $this;
    }

    public function getDecimalPlaces(): ?int
    {
        return $this->decimalPlaces ?? null;
    }

    public function setDecimalPlaces(?int $decimalPlaces): self
    {
        $this->decimalPlaces = $decimalPlaces;

        return $this;
    }

    public function getEnableOptionCreationDuringImport(): ?bool
    {
        return $this->enableOptionCreationDuringImport ?? null;
    }

    public function setEnableOptionCreationDuringImport(?bool $enableOptionCreationDuringImport): self
    {
        $this->enableOptionCreationDuringImport = $enableOptionCreationDuringImport;

        return $this;
    }

    public function getMaxItemsCount(): ?int
    {
        return $this->maxItemsCount ?? null;
    }

    public function setMaxItemsCount(?int $maxItemsCount): self
    {
        $this->maxItemsCount = $maxItemsCount;

        return $this;
    }
}
