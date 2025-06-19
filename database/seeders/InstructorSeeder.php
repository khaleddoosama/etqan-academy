<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InstructorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('instructors')->insert([
            [
                'name' => 'احمد موسي القاضي',
                'slug' => 'ahmed-mousy-alqadi',
                'description' => 'محاضر معتمد من شركه Adobe وأورا اكاديمي خبره 7 سنوات في مجال تصميم والمونتاج والموشن جرافيك',
            ]
        ]);
    }
}
