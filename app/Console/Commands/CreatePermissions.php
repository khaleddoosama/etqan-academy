<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;

class CreatePermissions extends Command
{
    protected $signature = 'permission:create {module} {permissions*}';
    protected $description = 'Create permissions for a module and assign them to user with ID = 1';


    public function handle()
    {
        $module = $this->argument('module');
        $permissions = $this->argument('permissions');

        $user = User::find(1);
        if (! $user) {
            $this->error('User with ID 1 not found.');
            return;
        }

        foreach ($permissions as $perm) {
            $permissionName = "{$module}.{$perm}";

            $permission = Permission::firstOrCreate(['name' => $permissionName], ['module' => $module, 'name' => $permissionName]);

            $user->givePermissionTo($permission);
            $this->info("Permission '{$permissionName}' created and assigned to user ID 1.");
        }

        $this->info('All permissions processed successfully.');
    }
}
