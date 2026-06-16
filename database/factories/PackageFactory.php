<?php

namespace Database\Factories;

use App\Models\Package;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Package>
 */
class PackageFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Pacote '.fake()->city(),
            'destination' => fake()->city(),
            'duration_days' => fake()->numberBetween(1, 15),
            'price' => fake()->randomFloat(2, 200, 5000),
            'description' => fake()->optional()->sentence(12),
            'active' => true,
        ];
    }
}
