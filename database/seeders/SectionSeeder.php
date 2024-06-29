<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('sections')->insert([
            'title' => 'المقدمة',
            'slug' => 's1-mmd',
            'description' => 'مقدمة',
            'course_id' => 1
        ]);
        DB::table('sections')->insert([
            'title' => 'المحتوى',
            'slug' => 's1-mhtw',
            'description' => 'المحتوى',
            'course_id' => 1
        ]);
        DB::table('sections')->insert([
            'title' => 'الختام',
            'slug' => 's1-khtm',
            'description' => 'الختام',
            'course_id' => 1
        ]);

        DB::table('sections')->insert([
            'title' => 'المقدمة',
            'slug' => 's2-mmd',
            'description' => 'مقدمة',
            'course_id' => 2
        ]);

        DB::table('sections')->insert([
            'title' => 'المحتوى',
            'slug' => 's2-mhtw',
            'description' => 'المحتوى',
            'course_id' => 2
        ]);

        DB::table('sections')->insert([
            'title' => 'الختام',
            'slug' => 's2-khtm',
            'description' => 'الختام',
            'course_id' => 2
        ]);

        DB::table('sections')->insert([
            'title' => 'المقدمة',
            'slug' => 's3-mmd',
            'description' => 'مقدمة',
            'course_id' => 3
        ]);
    }
}
