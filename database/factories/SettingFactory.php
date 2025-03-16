<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Setting;

class SettingFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Setting::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'seller_name' => fake()->word(),
            'seller_company_name' => fake()->word(),
            'seller_email' => fake()->word(),
            'seller_phone' => fake()->word(),
            'seller_address' => fake()->word(),
            'seller_city' => fake()->word(),
            'seller_postal_code' => fake()->word(),
            'seller_country' => fake()->word(),
            'seller_nip' => fake()->word(),
            'seller_regon' => fake()->word(),
            'seller_krs' => fake()->word(),
            'invoice_default_issuer' => fake()->word(),
            'invoice_default_place' => fake()->word(),
            'invoice_default_pattern' => fake()->word(),
            'invoice_default_tax_rate' => fake()->word(),
            'invoice_default_template' => fake()->word(),
        ];
    }
}
