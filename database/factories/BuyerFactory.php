<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Buyer;

class BuyerFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Buyer::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'company_name' => fake()->word(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->word(),
            'city' => fake()->city(),
            'postal_code' => fake()->postcode(),
            'country' => fake()->country(),
            'nip' => fake()->word(),
            'regon' => fake()->word(),
            'krs' => fake()->word(),
        ];
    }
}
