<?php

declare(strict_types=1);

namespace AkeneoLib\Entity;

class ReferenceEntityRecord
{
    use ValuesTrait;

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
}
