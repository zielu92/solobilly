<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('settings')->insert([
            [
                'key' => 'general.currencies',
                'value' => json_encode(["35", "84", "109"]),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'key' => 'general.default_currency',
                'value' => "84",
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
