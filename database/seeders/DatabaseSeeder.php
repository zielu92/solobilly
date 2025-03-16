<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Env;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $this->call([
            CurrencySeeder::class,
            SettingsSeeder::class,
        ]);

        if(!User::find(1)) {
            User::factory()->create([
                'name' => 'Admin',
                'email' => Env("FIRST_USER_EMAIL"),
                'password' => bcrypt(Env("FIRST_USER_PASSWORD"))
            ]);
        }
    }
}
