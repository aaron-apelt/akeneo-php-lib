<?php

declare(strict_types=1);

namespace AkeneoLib\Entity;

class Product
{
    use ValuesTrait;

    private ?string $uuid;

    private ?bool $enabled;

    private ?string $family;

    private ?array $categories;

    private ?array $groups;

    private ?string $parent;

    private ?array $associations;

    private ?array $quantifiedAssociations;

    private ?string $created;

    private ?string $updated;

    private ?array $metadata;

    private ?array $qualityScores;

    private ?array $completenesses;

    private ?string $rootParent;

    private ?array $workflowExecutionStatus;

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

    public function getUuid(): ?string
    {
        return $this->uuid ?? null;
    }

    public function setUuid(?string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function getGroups(): ?array
    {
        return $this->groups ?? null;
    }

    public function setGroups(?array $groups): self
    {
        $this->groups = $groups;

        return $this;
    }

    public function getAssociations(): ?array
    {
        return $this->associations ?? null;
    }

    public function setAssociations(?array $associations): self
    {
        $this->associations = $associations;

        return $this;
    }

    public function getQuantifiedAssociations(): ?array
    {
        return $this->quantifiedAssociations ?? null;
    }

    public function setQuantifiedAssociations(?array $quantifiedAssociations): self
    {
        $this->quantifiedAssociations = $quantifiedAssociations;

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

    public function getMetadata(): ?array
    {
        return $this->metadata ?? null;
    }

    public function setMetadata(?array $metadata): self
    {
        $this->metadata = $metadata;

        return $this;
    }

    public function getQualityScores(): ?array
    {
        return $this->qualityScores ?? null;
    }

    public function setQualityScores(?array $qualityScores): self
    {
        $this->qualityScores = $qualityScores;

        return $this;
    }

    public function getCompletenesses(): ?array
    {
        return $this->completenesses ?? null;
    }

    public function setCompletenesses(?array $completenesses): self
    {
        $this->completenesses = $completenesses;

        return $this;
    }

    public function getRootParent(): ?string
    {
        return $this->rootParent ?? null;
    }

    public function setRootParent(?string $rootParent): self
    {
        $this->rootParent = $rootParent;

        return $this;
    }

    public function getWorkflowExecutionStatus(): ?array
    {
        return $this->workflowExecutionStatus ?? null;
    }

    public function setWorkflowExecutionStatus(?array $workflowExecutionStatus): self
    {
        $this->workflowExecutionStatus = $workflowExecutionStatus;

        return $this;
    }
}
