<?php

namespace Database\Factories;

use App\Enum\TypeOfContract;
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
            'company_name' => fake()->company(),
            'email' => fake()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'postal_code' => fake()->postcode(),
            'country' => 'Poland',
            'nip' => fake()->regexify('[0-9]{10}'),
            'regon' => fake()->regexify('[0-9]{9}'),
            'krs' => fake()->regexify('[0-9]{10}'),
            'contract_type' => $this->faker->randomElement(TypeOfContract::values()),
            'contract_rate' => fake()->randomFloat(2, 31, 250),
            'color' => randomColorHex()
        ];
    }
}
