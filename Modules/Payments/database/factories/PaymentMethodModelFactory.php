<?php

namespace Modules\Payments\Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Payments\Models\PaymentMethodModel;

class PaymentMethodModelFactory extends Factory
{

    protected $model = PaymentMethodModel::class;

    public function definition(): array
    {
        return [
            "user_id"       => User::factory(),
            "name"          => fake()->word,
            "description"   => fake()->sentence(5),
            "method"        => fake()->randomElement(['cash', 'transfer']),
            "active"        => fake()->boolean()
        ];
    }

}
