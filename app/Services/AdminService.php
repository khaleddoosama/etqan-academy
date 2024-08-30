<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Validation\ValidationException;

class AdminService
{
    protected $roleService;

    public function __construct(RoleService $roleService)
    {
        $this->roleService = $roleService;
    }

    public function getAdmins()
    {
        return User::admin()->get();
    }

    public function getAdmin($id)
    {
        return User::admin()->find($id);
    }

    public function createAdmin(array $data)
    {
        $role = $this->roleService->getRole($data['role']);
        $data['role'] = 'admin';
        $data['status'] = 1;

        $admin = User::create($data);

        $admin->assignRole($role);

        return $admin;
    }


    // update user
    public function updateUser(array $data, User $admin)
    {
        $role = $this->roleService->getRole($data['role']);
        unset($data['role']);

        $admin->update($data);

        $admin->syncRoles($role);

        return $admin->wasChanged();
    }

    // delete user
    public function deleteUser(User $admin)
    {
        return $admin->delete();
    }
}
