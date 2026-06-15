<?php

namespace Database\Factories;

use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Vehicle>
 */
class VehicleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            // Placa no formato Mercosul (LLLNLNN), ex.: ABC1D23.
            'plate' => strtoupper(fake()->unique()->bothify('???#?##')),
            'model' => fake()->randomElement(['Paradiso G7', 'Viaggio', 'Audace', 'Ideale', 'New Marcopolo']),
            'brand' => fake()->randomElement(['Marcopolo', 'Comil', 'Mascarello', 'Busscar']),
            'capacity' => fake()->numberBetween(20, 60),
            'year' => fake()->numberBetween(2005, 2024),
            'active' => true,
        ];
    }
}
