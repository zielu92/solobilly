<?php

namespace Database\Factories;

use App\Models\Currency;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Buyer;
use App\Models\Invoice;
use App\Models\User;
use Modules\Payments\Models\PaymentMethodModel;

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
            'type' => fake()->randomElement([
                'regular',
                'proforma',
                'draft',
                'cancelled'
            ]),
            'payment_status' => fake()->randomElement([
                'paid',
                'not_paid'
            ]),
            'place' => fake()->word(),
            'sale_date' => fake()->date(),
            'due_date' => fake()->date(),
            'issue_date' => fake()->date(),
            'user_id' => User::factory(),
            'comment' => fake()->word(),
            'currency_id' => Currency::inRandomOrder()->first()->id,
            'issuer_name' => fake()->word(),
            'grand_total_net' => fake()->randomFloat(2, 0, 99999999.99),
            'grand_total_gross' => fake()->randomFloat(2, 0, 99999999.99),
            'grand_total_tax' => fake()->randomFloat(2, 0, 99999999.99),
            'grand_total_discount' => fake()->randomFloat(2, 0, 99999999.99),
            'paid' => fake()->randomFloat(2, 0, 99999999.99),
            'due' => fake()->randomFloat(2, 0, 99999999.99),
            'payment_method_id' => PaymentMethodModel::factory(),
        ];
    }
}
