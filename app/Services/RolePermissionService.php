<?php

namespace App\Services;


class RolePermissionService
{
    //updateRolePermissions
    public function updateRolePermissions($data, $role)
    {
        $role->syncPermissions($data['permissions']);

    }
}
