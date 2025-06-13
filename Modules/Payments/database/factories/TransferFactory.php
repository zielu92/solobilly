<?php

namespace Modules\Payments\Database\Factories;

use Modules\Payments\Payments\Transfer;
use Illuminate\Database\Eloquent\Factories\Factory;
class TransferFactory extends Factory
{
    protected $model = Transfer::class;

    public function definition(): array
    {
        return [
            'accountNumber' => fake()->randomNumber(12),
            'bankName' => fake()->company . ' Bank',
            'iban' => fake()->iban(),
            'swift' => fake()->swiftBicNumber(),
            'beneficiaryName' => fake()->name(),
            'beneficiaryAddress' => fake()->address(),
        ];
    }
}
