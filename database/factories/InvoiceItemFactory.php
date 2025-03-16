<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Invoice;
use App\Models\InvoiceItem;

class InvoiceItemFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = InvoiceItem::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'quantity' => fake()->word(),
            'price_net' => fake()->randomFloat(2, 0, 99999999.99),
            'price_gross' => fake()->randomFloat(2, 0, 99999999.99),
            'tax_rate' => fake()->word(),
            'tax_amount' => fake()->randomFloat(2, 0, 99999999.99),
            'discount' => fake()->randomFloat(2, 0, 99999999.99),
            'discount_type' => fake()->word(),
            'total_net' => fake()->randomFloat(2, 0, 99999999.99),
            'total_gross' => fake()->randomFloat(2, 0, 99999999.99),
            'total_tax' => fake()->randomFloat(2, 0, 99999999.99),
            'total_discount' => fake()->randomFloat(2, 0, 99999999.99),
            'invoice_id' => Invoice::factory(),
        ];
    }
}
