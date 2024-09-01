<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            'dashboard' => [
                'dashboard.list',
            ],
            'user' => [
                'user.list',
                'user.create',
                'user.show',
                'user.edit',
                'user.status',
            ],
            'category' => [
                'category.list',
                'category.create',
                'category.edit',
                'category.delete',
            ],
            'program' => [
                'program.list',
                'program.create',
                'program.edit',
                'program.delete',
            ],
            'instructor' => [
                'instructor.list',
                'instructor.create',
                'instructor.edit',
                'instructor.delete',
            ],
            'course' => [
                'course.list',
                'course.create',
                'course.edit',
                'course.show',
                'course.status',
            ],
            'user_course' => [
                'user_course.list',
                'user_course.create',
                'user_course.status',
            ],
            'permission' => [
                'permission.list',
                'permission.create',
                'permission.edit',
                'permission.delete',
            ],
            'role' => [
                'role.list',
                'role.create',
                'role.edit',
                'role.delete',
            ],
            'role_permission' => [
                'role_permission.list',
                'role_permission.edit',
            ],
            'admin' => [
                'admin.list',
                'admin.create',
                'admin.edit',
                'admin.delete',
            ],
            'inquiry' => [
                'inquiry.list',
                'inquiry.show',
                'inquiry.reply',
            ],
            'withdrawal' => [
                'withdrawal.list',
                'withdrawal.show',
                'withdrawal.status',
            ],
            'request_course' => [
                'request_course.list',
                'request_course.show',
                'request_course.status',
            ],
        ];

        // Insert permissions
        foreach ($permissions as $module => $names) {
            foreach ($names as $name) {
                DB::table('permissions')->insert([
                    'name' => $name,
                    'guard_name' => 'web',
                    'module' => $module,
                ]);
            }
        }
    }
}
