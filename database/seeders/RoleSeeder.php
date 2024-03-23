<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;



class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->insert([
            'name' => 'Super Admin',
            'guard_name' => 'web',
        ]);
        // assign role with all permissions
        $role = Role::find(1);
        $role->givePermissionTo(DB::table('permissions')->pluck('name'));

        DB::table('roles')->insert([
            'name' => 'Admin',
            'guard_name' => 'web',
        ]);

        DB::table('roles')->insert([
            'name' => 'CEO',
            'guard_name' => 'web',
        ]);

        DB::table('roles')->insert([
            'name' => 'Account',
            'guard_name' => 'web',
        ]);
    }
}
