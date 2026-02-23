<?php

namespace App\Compat\Database;

class CiResult
{
    public function __construct(private readonly array $rows)
    {
    }

    public function getResultArray(): array
    {
        return $this->rows;
    }

    public function getRowArray(): ?array
    {
        return $this->rows[0] ?? null;
    }

    public function getRow(): ?object
    {
        $row = $this->getRowArray();
        return $row ? (object) $row : null;
    }
}
