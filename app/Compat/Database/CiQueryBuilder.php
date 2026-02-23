<?php

namespace App\Compat\Database;

use Illuminate\Support\Facades\DB;

class CiQueryBuilder
{
    private mixed $builder;

    public function __construct(string $table)
    {
        $this->builder = DB::table($this->normalizeTable($table));
    }

    public function select(string|array $columns): self
    {
        $this->builder->selectRaw(is_array($columns) ? implode(',', $columns) : $columns);
        return $this;
    }

    public function join(string $table, string $condition, string $type = 'inner'): self
    {
        $joinType = strtolower(trim($type));
        $joinMethod = match ($joinType) {
            'left' => 'leftJoin',
            'right' => 'rightJoin',
            default => 'join',
        };

        $normalizedTable = $this->normalizeTable($table);
        $normalizedCondition = trim(preg_replace('/\s+/', ' ', $condition));

        if (substr_count($normalizedCondition, '=') === 1
            && !preg_match('/\b(AND|OR|BETWEEN|LIKE|IS|IN)\b/i', $normalizedCondition)
        ) {
            [$left, $right] = array_map('trim', explode('=', $normalizedCondition, 2));
            $this->builder->{$joinMethod}($normalizedTable, $left, '=', $right);
            return $this;
        }

        // Complex ON clauses from CI4 are executed as raw SQL to preserve behavior.
        $this->builder->{$joinMethod}($normalizedTable, function ($join) use ($normalizedCondition) {
            $join->whereRaw($normalizedCondition);
        });
        return $this;
    }

    public function where(string $key, mixed $value = null, bool $escape = true): self
    {
        if ($value === null && str_contains($key, ' IS NOT NULL')) {
            $field = trim(str_replace('IS NOT NULL', '', $key));
            $this->builder->whereNotNull($field);
            return $this;
        }

        if ($value === null && str_contains($key, ' IS NULL')) {
            $field = trim(str_replace('IS NULL', '', $key));
            $this->builder->whereNull($field);
            return $this;
        }

        if (!$escape && $value === null) {
            $this->builder->whereRaw($key);
            return $this;
        }

        if ($value === null) {
            // CI4 often passes SQL expressions as first arg without explicit escape=false.
            if (preg_match('/[\\s\\(\\)\\+\\-\\/*]|DAYOFYEAR|CURDATE|NOW|COUNT|SUM/i', $key)) {
                $this->builder->whereRaw($key);
                return $this;
            }
            $this->builder->where($key);
            return $this;
        }

        if (preg_match('/^(.+)\s*(>=|<=|<>|!=|>|<)$/', $key, $m)) {
            $this->builder->where(trim($m[1]), $m[2], $value);
            return $this;
        }

        $this->builder->where($key, $value);
        return $this;
    }

    public function orWhere(string $key, mixed $value = null): self
    {
        $this->builder->orWhere($key, $value);
        return $this;
    }

    public function whereIn(string $key, array $values): self
    {
        $this->builder->whereIn($key, $values);
        return $this;
    }

    public function like(string $field, string $term): self
    {
        $this->builder->where($field, 'like', '%' . $term . '%');
        return $this;
    }

    public function orLike(string $field, string $term): self
    {
        $this->builder->orWhere($field, 'like', '%' . $term . '%');
        return $this;
    }

    public function orderBy(string $field, string $direction = 'ASC'): self
    {
        if (preg_match('/[\\(\\)\\s]|DAYOFYEAR|CURDATE|NOW|COUNT|SUM/i', $field)) {
            $this->builder->orderByRaw($field . ' ' . $direction);
            return $this;
        }
        $this->builder->orderBy($field, $direction);
        return $this;
    }

    public function groupBy(string|array $fields): self
    {
        $this->builder->groupBy($fields);
        return $this;
    }

    public function having(string $key, mixed $value = null): self
    {
        if ($value === null) {
            $this->builder->havingRaw($key);
        } else {
            $this->builder->having($key, $value);
        }

        return $this;
    }

    public function limit(int $limit, ?int $offset = null): self
    {
        $this->builder->limit($limit);
        if ($offset !== null) {
            $this->builder->offset($offset);
        }

        return $this;
    }

    public function groupStart(): self
    {
        return $this;
    }

    public function groupEnd(): self
    {
        return $this;
    }

    public function countAllResults(bool $reset = true): int
    {
        $count = (clone $this->builder)->count();
        return $count;
    }

    public function get(?int $limit = null): CiResult
    {
        if ($limit !== null) {
            $this->builder->limit($limit);
        }

        $rows = $this->builder->get()->map(fn ($row) => (array) $row)->all();
        return new CiResult($rows);
    }

    public function insert(array $data): bool
    {
        return $this->builder->insert($data);
    }

    public function insertBatch(array $rows): bool
    {
        return $this->builder->insert($rows);
    }

    public function set(array|string $key, mixed $value = null): self
    {
        if (!property_exists($this, 'setPayload')) {
            $this->setPayload = [];
        }

        if (is_array($key)) {
            $this->setPayload = array_merge($this->setPayload, $key);
        } else {
            $this->setPayload[$key] = $value;
        }

        return $this;
    }

    public function update(array $data = []): bool
    {
        $payload = $data;
        if (property_exists($this, 'setPayload') && !empty($this->setPayload)) {
            $payload = array_merge($this->setPayload, $payload);
            $this->setPayload = [];
        }

        return $this->builder->update($payload);
    }

    public function delete(): bool
    {
        return $this->builder->delete() > 0;
    }

    private function normalizeTable(string $table): string
    {
        $table = trim($table);
        if (str_contains(strtolower($table), ' as ')) {
            return $table;
        }

        if (preg_match('/^([a-zA-Z0-9_`]+)\\s+([a-zA-Z0-9_`]+)$/', $table, $m)) {
            return $m[1] . ' as ' . $m[2];
        }

        return $table;
    }
}
