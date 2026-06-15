<?php

namespace Database\Factories;

use App\Enums\TripStatus;
use App\Models\Driver;
use App\Models\Trip;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Trip>
 */
class TripFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $cities = ['Pelotas', 'Gramado', 'Porto Alegre', 'Rio Grande', 'Capão da Canoa', 'Bento Gonçalves'];

        $departure = fake()->dateTimeBetween('-1 month', '+2 months');
        // Chegada sempre posterior à partida (regra do CLAUDE.md).
        $arrival = (clone $departure)->modify('+'.fake()->numberBetween(2, 12).' hours');

        return [
            'origin' => fake()->randomElement($cities),
            'destination' => fake()->randomElement($cities),
            'departure_at' => $departure,
            'arrival_at' => $arrival,
            'status' => fake()->randomElement(TripStatus::cases()),
            'vehicle_id' => Vehicle::factory(),
            'driver_id' => Driver::factory(),
        ];
    }
}
