<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Farmer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Farmer>
 */
class FarmerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'identifier' => fake()->unique()->numerify('FARM-####'),
            'firstname' => fake()->firstName(),
            'lastname' => fake()->lastName(),
            'phone' => fake()->unique()->numerify('+2250#########'),
            'credit_limit' => 500000,
        ];
    }
}
