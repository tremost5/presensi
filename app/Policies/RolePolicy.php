<?php

namespace App\Policies;

class RolePolicy
{
    public function access(int|string $roleId): bool
    {
        return (string) session('role_id') === (string) $roleId;
    }
}
