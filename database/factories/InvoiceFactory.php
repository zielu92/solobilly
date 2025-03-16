<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Buyer;
use App\Models\Invoice;
use App\Models\User;

class InvoiceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Invoice::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'no' => fake()->word(),
            'buyer_id' => Buyer::factory(),
            'type' => fake()->word(),
            'status' => fake()->word(),
            'payment_status' => fake()->word(),
            'place' => fake()->word(),
            'sale_date' => fake()->date(),
            'due_date' => fake()->date(),
            'issue_date' => fake()->word(),
            'parent_id' => Invoice::factory(),
            'user_id' => User::factory(),
            'comment' => fake()->word(),
            'currency' => fake()->word(),
            'issuer_name' => fake()->word(),
            'grand_total_net' => fake()->randomFloat(2, 0, 99999999.99),
            'grand_total_gross' => fake()->randomFloat(2, 0, 99999999.99),
            'grand_total_tax' => fake()->randomFloat(2, 0, 99999999.99),
            'grand_total_discount' => fake()->randomFloat(2, 0, 99999999.99),
            'paid' => fake()->randomFloat(2, 0, 99999999.99),
            'due' => fake()->randomFloat(2, 0, 99999999.99),
            'path' => fake()->word(),
        ];
    }
}
