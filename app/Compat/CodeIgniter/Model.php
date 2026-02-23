<?php

namespace CodeIgniter;

use Config\Database;

class Model
{
    protected $table;
    protected $primaryKey = 'id';
    protected $allowedFields = [];
    protected $useTimestamps = false;
    private mixed $builder = null;
    private int|string $lastInsertId = 0;

    public function __construct()
    {
    }

    protected function builder(): mixed
    {
        return $this->builder ??= Database::connect()->table($this->table);
    }

    protected function resetBuilder(): void
    {
        $this->builder = null;
    }

    public function where(string $key, mixed $value = null, bool $escape = true): static
    {
        $this->builder()->where($key, $value, $escape);
        return $this;
    }

    public function join(string $table, string $condition, string $type = 'inner'): static
    {
        $this->builder()->join($table, $condition, $type);
        return $this;
    }

    public function select(string|array $columns): static
    {
        $this->builder()->select($columns);
        return $this;
    }

    public function orderBy(string $field, string $direction = 'ASC'): static
    {
        $this->builder()->orderBy($field, $direction);
        return $this;
    }

    public function set(array|string $key, mixed $value = null): static
    {
        $this->builder()->set($key, $value);
        return $this;
    }

    public function first(): ?array
    {
        $row = $this->builder()->get(1)->getRowArray();
        $this->resetBuilder();
        return $row;
    }

    public function find(int|string $id): ?array
    {
        $row = Database::connect()->table($this->table)
            ->where($this->primaryKey, $id)
            ->get(1)
            ->getRowArray();
        return $row;
    }

    public function findAll(?int $limit = null): array
    {
        $rows = $this->builder()->get($limit)->getResultArray();
        $this->resetBuilder();
        return $rows;
    }

    public function insert(array $data): bool
    {
        $payload = $this->filter($data);
        $db = Database::connect();
        $ok = $db->table($this->table)->insert($payload);
        $this->lastInsertId = (int) ($db->insertID() ?? 0);
        $this->resetBuilder();
        return $ok;
    }

    public function getInsertID(): int|string
    {
        return $this->lastInsertId;
    }

    public function update(int|string|null $id = null, array $data = []): bool
    {
        $payload = $this->filter($data);
        $builder = $this->builder();

        if ($id !== null) {
            $builder->where($this->primaryKey, $id);
        }

        $ok = $builder->update($payload);
        $this->resetBuilder();
        return $ok;
    }

    public function delete(int|string|null $id = null): bool
    {
        $builder = Database::connect()->table($this->table);
        if ($id !== null) {
            $builder->where($this->primaryKey, $id);
        }

        return $builder->delete();
    }

    private function filter(array $data): array
    {
        if (empty($this->allowedFields)) {
            return $data;
        }

        return array_intersect_key($data, array_flip($this->allowedFields));
    }
}
