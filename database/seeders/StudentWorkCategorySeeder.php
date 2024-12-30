<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudentWorkCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('student_work_categories')->insert([
            'name' => 'اعمال الطلاب في تصميم السوشيال ميديا',
            'slug' => 'aamal-alptlab-f-tsmym-al-soshial-midya'
        ]);

        DB::table('student_work_categories')->insert([
            'name' => 'اعمال الطلاب في تصميم الجرافيكي',
            'slug' => 'aamal-alptlab-f-tsmym-al-jrafiki'
        ]);

        DB::table('student_work_categories')->insert([
            'name' => 'اعمال الطلاب في تصميم الموشن جرافيك',
            'slug' => 'aamal-alptlab-f-tsmym-al-moshon-jrafiki'
        ]);
    }
}
