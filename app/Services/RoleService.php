<?php
// app/Services/CategoryService.php

namespace App\Services;


use Illuminate\Database\Eloquent\Collection;
use Spatie\Permission\Models\Role;

class RoleService
{
    public function getAllRoles(): Collection
    {
        return Role::all();
    }

    public function createRole(array $data): Role
    {
        return Role::create($data);
    }

    public function updateRole(Role $role, array $data): bool
    {
        $role->update($data);

        return $role->wasChanged();
    }

    public function deleteRole(Role $role): bool
    {
        return $role->delete();
    }
}
