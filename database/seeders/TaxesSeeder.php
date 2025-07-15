<?php

namespace Database\Seeders;

use App\Models\Tax;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaxesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $taxes = [23,8,5,0];
        foreach ($taxes as $tax) {
            Tax::updateOrCreate([
                'name' => $tax.'%',
                'rate' => $tax,
            ]);
        }

    }
}
