<?php

declare(strict_types=1);

namespace AkeneoLib\Adapter;

use AkeneoLib\Entity\ReferenceEntity;

class ReferenceEntityAdapter implements ReferenceEntityAdapterInterface
{
    // Example: replace $dataSource with real data source or API client
    private $dataSource;

    public function __construct($dataSource)
    {
        $this->dataSource = $dataSource;
    }

    public function getReferenceEntity(string $code): ?ReferenceEntity
    {
        $entityData = $this->dataSource->findReferenceEntityByCode($code);
        if (!$entityData) {
            return null;
        }
        return new ReferenceEntity($entityData['code'], $entityData['labels'] ?? null);
    }

    public function getAllReferenceEntities(): array
    {
        $entities = [];
        foreach ($this->dataSource->findAllReferenceEntities() as $entityData) {
            $entities[] = new ReferenceEntity($entityData['code'], $entityData['labels'] ?? null);
        }
        return $entities;
    }
}