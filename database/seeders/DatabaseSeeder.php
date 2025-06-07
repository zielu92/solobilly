<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Chiiya\FilamentAccessControl\Database\Seeders\FilamentAccessControlSeeder;
use Chiiya\FilamentAccessControl\Enumerators\RoleName;
use Illuminate\Database\Seeder;
use Illuminate\Support\Env;
use Spatie\Permission\Models\Role;

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
            FilamentAccessControlSeeder::class,
            PermissionsAndRolesSeeder::class
        ]);

        if(!User::find(1)) {
            $userDetails = [
                'first_name' => 'Admin',
                'email' => Env("FIRST_USER_EMAIL"),
                'password' => bcrypt(Env("FIRST_USER_PASSWORD"))
            ];
            $user = User::query()->create($userDetails);
            $user->assignRole(RoleName::SUPER_ADMIN);
            $user->save();
        }
    }
}
