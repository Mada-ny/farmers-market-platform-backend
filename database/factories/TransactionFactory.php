<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\PaymentMethod;
use App\Models\Farmer;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Transaction>
 */
class TransactionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'farmer_id' => Farmer::factory(),
            'operator_id' => User::factory()->operator(),
            'total_fcfa' => fake()->randomFloat(2, 500, 50000),
            'payment_method' => PaymentMethod::Cash,
            'interest_rate' => null,
            'credited_amount' => null,
        ];
    }

    public function credit(float $interestRate = 0.10): static
    {
        return $this->state(function (array $attributes) use ($interestRate) {
            $total = (float) $attributes['total_fcfa'];

            return [
                'payment_method' => PaymentMethod::Credit,
                'interest_rate' => $interestRate,
                'credited_amount' => round($total * (1 + $interestRate), 2),
            ];
        });
    }
}
