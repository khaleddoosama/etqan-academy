<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CouponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('coupons')->insert([
            [
                'code' => 'khaled',
                'discount' => 10,
                'type' => 'percentage',
                'expires_at' => now()->addDays(30),
                'status' => 1
            ]
            ]);
    }
}
