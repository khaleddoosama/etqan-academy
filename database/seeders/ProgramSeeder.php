<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProgramSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('programs')->insert([
            [
                'name' => 'PHP',
                'slug' => 'php',
                'description' => 'PHP is a general-purpose scripting language that is especially suited to web development.',
                'icon' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/3/3d/PHP-logo.svg/1200px-PHP-logo.svg.png',
            ],
            [
                'name' => 'Java',
                'slug' => 'java',
                'description' => 'Java is a high-level, class-based, object-oriented programming language.',
                'icon' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/1/18/ISO_C%2B%2B_Logo.svg/1200px-ISO_C%2B%2B_Logo.svg.png',
            ],
            [
                'name' => 'JavaScript',
                'slug' => 'javascript',
                'description' => 'JavaScript, often abbreviated as JS, is a programming language that conforms to the ECMAScript specification.',
                'icon' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/9/99/Unofficial_JavaScript_logo_2.svg/1200px-Unofficial_JavaScript_logo_2.svg.png',
            ],
        ]);
    }
}
