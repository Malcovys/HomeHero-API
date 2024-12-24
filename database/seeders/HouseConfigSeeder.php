<?php

namespace Database\Seeders;

use DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HouseConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('house_configs')->insert([
            [
                'automatise_task_management' => true
            ],
            [
                'automatise_task_management' => false
            ],
        ]);
    }
}
