<?php

declare(strict_types=1);

namespace AkeneoLib\Adapter;

use AkeneoLib\Entity\ReferenceEntity;

interface ReferenceEntityAdapterInterface
{
    public function getReferenceEntity(string $code): ?ReferenceEntity;
    public function getAllReferenceEntities(): array;
}