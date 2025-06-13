<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Cost;
use App\Models\CostCategory;
use App\Models\User;

class CostFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Cost::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'amount' => fake()->randomFloat(2, 0, 99999999.99),
            'description' => fake()->text(),
            'date' => fake()->date(),
            'category_id' => CostCategory::factory(),
            'invoice_number' => fake()->word(),
//            'invoice_file_path' => fake()->word(),
//            'receipt_file_path' => fake()->word(),
            'invoice_date' => fake()->date(),
            'invoice_due_date' => fake()->date(),
            'payment_date' => fake()->date(),
            'user_id' => User::factory(),
        ];
    }
}
