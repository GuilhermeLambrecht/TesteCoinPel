<?php

namespace Database\Factories;

use App\Enums\ContractStatus;
use App\Models\Client;
use App\Models\Contract;
use App\Models\Package;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Contract>
 */
class ContractFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Vigência sempre válida: término >= início.
        $start = fake()->dateTimeBetween('-1 month', '+2 months');
        $end = (clone $start)->modify('+'.fake()->numberBetween(1, 30).' days');

        return [
            'title' => 'Contrato '.fake()->numerify('CT-#####'),
            'client_id' => Client::factory(),
            'package_id' => Package::factory(),
            'start_date' => $start,
            'end_date' => $end,
            'value' => fake()->randomFloat(2, 500, 20000),
            'status' => fake()->randomElement(ContractStatus::cases())->value,
        ];
    }
}
