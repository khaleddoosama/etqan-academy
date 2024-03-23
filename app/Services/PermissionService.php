<?php
// app/Services/CategoryService.php

namespace App\Services;


use Illuminate\Database\Eloquent\Collection;
use Spatie\Permission\Models\Permission;

class PermissionService
{
    public function getAllPermissions(): Collection
    {
        return Permission::all();
    }

    public function createPermission(array $data): Permission
    {
        return Permission::create($data);
    }

    public function updatePermission(Permission $permission, array $data): bool
    {
        $permission->update($data);

        return $permission->wasChanged();
    }

    public function deletePermission(Permission $permission): bool
    {
        return $permission->delete();
    }
}
