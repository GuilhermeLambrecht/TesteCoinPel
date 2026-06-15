<?php

namespace Database\Factories;

use App\Models\Driver;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Driver>
 */
class DriverFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            // CPF/CNH apenas como dígitos (faker en_US não tem cpf()).
            'cpf' => fake()->unique()->numerify('###########'),
            'cnh' => fake()->numerify('###########'),
            'cnh_category' => fake()->randomElement(['A', 'B', 'C', 'D', 'E']),
            'cnh_expiration' => fake()->dateTimeBetween('+1 year', '+5 years'),
            'phone' => fake()->numerify('(##) #####-####'),
            'active' => true,
        ];
    }
}
