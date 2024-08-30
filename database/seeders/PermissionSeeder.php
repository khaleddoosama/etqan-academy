<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        DB::table('permissions')->insert(
            [
                [
                    'name' => 'dashboard.list',
                    'guard_name' => 'web',
                    'module' => 'dashboard',
                ]
            ]
        );

        DB::table('permissions')->insert(
            [
                [
                    'name' => 'user.list',
                    'guard_name' => 'web',
                    'module' => 'user',
                ],
                [
                    'name' => 'user.create',
                    'guard_name' => 'web',
                    'module' => 'user',
                ],
                [
                    'name' => 'user.edit',
                    'guard_name' => 'web',
                    'module' => 'user',
                ],
                [
                    'name' => 'user.status',
                    'guard_name' => 'web',
                    'module' => 'user',
                ]
            ]
        );

        DB::table('permissions')->insert(
            [
                [
                    'name' => 'category.list',
                    'guard_name' => 'web',
                    'module' => 'category',
                ],
                [
                    'name' => 'category.create',
                    'guard_name' => 'web',
                    'module' => 'category',
                ],
                [
                    'name' => 'category.edit',
                    'guard_name' => 'web',
                    'module' => 'category',
                ],
                [
                    'name' => 'category.delete',
                    'guard_name' => 'web',
                    'module' => 'category',
                ]
            ]
        );

        DB::table('permissions')->insert(
            [
                [
                    'name' => 'program.list',
                    'guard_name' => 'web',
                    'module' => 'program',
                ],
                [
                    'name' => 'program.create',
                    'guard_name' => 'web',
                    'module' => 'program',
                ],
                [
                    'name' => 'program.edit',
                    'guard_name' => 'web',
                    'module' => 'program',
                ],
                [
                    'name' => 'program.delete',
                    'guard_name' => 'web',
                    'module' => 'program',
                ]
            ]
        );

        DB::table('permissions')->insert(
            [
                [
                    'name' => 'instructor.list',
                    'guard_name' => 'web',
                    'module' => 'instructor',
                ],
                [
                    'name' => 'instructor.create',
                    'guard_name' => 'web',
                    'module' => 'instructor',
                ],
                [
                    'name' => 'instructor.edit',
                    'guard_name' => 'web',
                    'module' => 'instructor',
                ],
                [
                    'name' => 'instructor.delete',
                    'guard_name' => 'web',
                    'module' => 'instructor',
                ]
            ]
        );

        DB::table('permissions')->insert(
            [
                [
                    'name' => 'course.list',
                    'guard_name' => 'web',
                    'module' => 'course',
                ],
                [
                    'name' => 'course.create',
                    'guard_name' => 'web',
                    'module' => 'course',
                ],
                [
                    'name' => 'course.edit',
                    'guard_name' => 'web',
                    'module' => 'course',
                ],
                [
                    'name' => 'course.delete',
                    'guard_name' => 'web',
                    'module' => 'course',
                ]
            ]
        );

        DB::table('permissions')->insert(
            [
                [
                    'name' => 'permission.list',
                    'guard_name' => 'web',
                    'module' => 'permission',
                ],
                [
                    'name' => 'permission.create',
                    'guard_name' => 'web',
                    'module' => 'permission',
                ],
                [
                    'name' => 'permission.edit',
                    'guard_name' => 'web',
                    'module' => 'permission',
                ],
                [
                    'name' => 'permission.delete',
                    'guard_name' => 'web',
                    'module' => 'permission',
                ]
            ]
        );

        DB::table('permissions')->insert(
            [
                [
                    'name' => 'role.list',
                    'guard_name' => 'web',
                    'module' => 'role',
                ],
                [
                    'name' => 'role.create',
                    'guard_name' => 'web',
                    'module' => 'role',
                ],
                [
                    'name' => 'role.edit',
                    'guard_name' => 'web',
                    'module' => 'role',
                ],
                [
                    'name' => 'role.delete',
                    'guard_name' => 'web',
                    'module' => 'role',
                ]
            ]
        );

        DB::table('permissions')->insert(
            [
                [
                    'name' => 'admin.list',
                    'guard_name' => 'web',
                    'module' => 'admin',
                ],
                [
                    'name' => 'admin.create',
                    'guard_name' => 'web',
                    'module' => 'admin',
                ],
                [
                    'name' => 'admin.edit',
                    'guard_name' => 'web',
                    'module' => 'admin',
                ],
                [
                    'name' => 'admin.delete',
                    'guard_name' => 'web',
                    'module' => 'admin',
                ]
            ]
        );

        DB::table('permissions')->insert(
            [
                [
                    'name' => 'inquiry.list',
                    'guard_name' => 'web',
                    'module' => 'inquiry',
                ],
                [
                    'name' => 'inquiry.reply',
                    'guard_name' => 'web',
                    'module' => 'inquiry',
                ]
            ]
        );

        DB::table('permissions')->insert(
            [
                [
                    'name' => 'Withdrawal.list',
                    'guard_name' => 'web',
                    'module' => 'Withdrawal',
                ],
                [
                    'name' => 'Withdrawal.reply',
                    'guard_name' => 'web',
                    'module' => 'Withdrawal',
                ]
            ]
        );

        DB::table('permissions')->insert(
            [
                [
                    'name' => 'request_course.list',
                    'guard_name' => 'web',
                    'module' => 'request_course',
                ],
                [
                    'name' => 'request_course.reply',
                    'guard_name' => 'web',
                    'module' => 'request_course',
                ]
            ]
        );
    }
}
