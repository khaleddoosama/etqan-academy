<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('courses')->insert([
            'title' => 'تكنولوجيا المعلومات',
            'slug' => 'tkn-mal',
            'description' => 'تكنولوجيا المعلومات',
            'category_id' => 1,
            'instructor_id' => 1
        ]);

        DB::table('courses')->insert([
            'title' => 'التصميم الجرافيكي',
            'slug' => 'tsmym-jr-fyk-y',
            'description' => 'التصميم الجرافيكي',
            'category_id' => 1,
            'instructor_id' => 1
        ]);

        DB::table('courses')->insert([
            'title' => 'التصميم الويب',
            'slug' => 'tsmym-wb',
            'description' => 'التصميم الويب',
            'category_id' => 1,
            'instructor_id' => 1
        ]);
    }
}
