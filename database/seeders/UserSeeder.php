<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define the path to the folder you want to delete
        $folderPath = public_path('uploads/user/pictures');
        $folderPath2 = public_path('uploads/admin/pictures');

        // Check if the folder exists
        if (File::isDirectory($folderPath)) {
            File::cleanDirectory($folderPath);
        }

        // Check if the folder exists
        if (File::isDirectory($folderPath2)) {
            File::cleanDirectory($folderPath2);
        }

        DB::table('users')->insert([
            [
                'first_name' => 'admin',
                'last_name' => 'admin',
                'slug' => 'admin',
                'code' => 'admin',
                'email' => 'admin@gmail.com',
                'password' => bcrypt('111'),
                'role' => 'admin',
                'status' => 1,
            ],
            [
                'first_name' => 'instructor',
                'last_name' => 'instructor',
                'slug' => 'instructor',
                'code' => 'instructor',
                'email' => 'instructor@gmail.com',
                'password' => bcrypt('111'),
                'role' => 'instructor',
                'status' => 1,
            ],
            [
                'first_name' => 'student',
                'last_name' => 'student',
                'slug' => 'student',
                'code' => 'student',
                'email' => 'student@gmail.com',
                'password' => bcrypt('111'),
                'role' => 'student',
                'status' => 1,
            ],
        ]);
        $admin = User::find(1);
        $admin->assignRole('Super Admin');
    }
}
