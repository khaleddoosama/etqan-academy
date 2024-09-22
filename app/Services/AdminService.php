<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
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
        // Adding cache for retrieving admins
        return Cache::remember('admins', 60, function () {
            return User::admin()->get();
        });
    }

    public function getAdmin($id)
    {
        return User::admin()->findOrFail($id);
    }

    public function createAdmin(array $data)
    {
        $role = $this->roleService->getRole($data['role']);
        $data['role'] = 'admin';
        $data['status'] = 1;

        $admin = User::create($data);

        $admin->assignRole($role);

        // Clear cache after deleting an admin
        Cache::forget('admins');

        return $admin;
    }


    // update user
    public function updateAdmin(array $data, User $admin)
    {
        $role = $this->roleService->getRole($data['role']);
        unset($data['role']);

        $admin->update($data);

        $admin->syncRoles($role);

        // Clear cache after deleting an admin
        Cache::forget('admins');

        return $admin->wasChanged();
    }

    // delete user
    public function deleteAdmin(User $admin)
    {
        $result = $admin->delete();

        // Clear cache after deleting an admin
        Cache::forget('admins');

        return $result;
    }
}
