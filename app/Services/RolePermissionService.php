<?php

namespace App\Services;


class RolePermissionService
{
    protected  $permissionService;
    protected  $roleService;

    public function __construct(PermissionService $permissionService, RoleService $roleService)
    {
        $this->permissionService = $permissionService;
        $this->roleService = $roleService;
    }

    public function getAllRoles()
    {
        return $this->roleService->getAllRoles();
    }

    public function getRole(int $id)
    {
        return $this->roleService->getRole($id);
    }

    public function getPermissionModules()
    {
        return $this->permissionService->getPermissionModules();
    }

    //updateRolePermissions
    public function updateRolePermissions($data, $role)
    {
        $role->syncPermissions($data['permissions']);

    }
}
