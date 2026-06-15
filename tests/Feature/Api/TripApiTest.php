<?php

namespace Tests\Feature\Api;

use App\Enums\TripStatus;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class TripApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_trips_endpoint_requires_authentication(): void
    {
        Trip::factory()->create();

        // Sem token: a API administrativa não é pública.
        $this->getJson('/api/trips')->assertUnauthorized();
    }

    public function test_trips_endpoint_returns_minimized_json_shape(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $trip = Trip::factory()->create([
            'origin' => 'Pelotas',
            'destination' => 'Gramado',
        ]);

        $response = $this->getJson('/api/trips');

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    [
                        'id', 'origin', 'destination', 'departure_at', 'arrival_at',
                        'vehicle' => ['id', 'plate', 'model'],
                        'driver' => ['id', 'name'],
                    ],
                ],
                'links' => ['first', 'last', 'prev', 'next'],
                'meta' => ['current_page', 'last_page', 'per_page', 'total'],
            ])
            ->assertJsonPath('data.0.origin', 'Pelotas')
            ->assertJsonPath('data.0.vehicle.id', $trip->vehicle_id)
            ->assertJsonPath('data.0.driver.id', $trip->driver_id);

        // Dados pessoais do motorista NÃO podem aparecer (minimização / LGPD).
        $driver = $response->json('data.0.driver');
        $this->assertArrayNotHasKey('cpf', $driver);
        $this->assertArrayNotHasKey('cnh', $driver);
        $this->assertArrayNotHasKey('phone', $driver);
        $response->assertJsonMissing(['cpf' => $trip->driver->cpf]);

        // Veículo também expõe apenas o essencial.
        $vehicle = $response->json('data.0.vehicle');
        $this->assertArrayNotHasKey('brand', $vehicle);
        $this->assertArrayNotHasKey('capacity', $vehicle);
    }

    public function test_trips_pagination_respects_per_page(): void
    {
        Sanctum::actingAs(User::factory()->create());

        Trip::factory()->count(20)->create();

        $this->getJson('/api/trips')
            ->assertOk()
            ->assertJsonCount(15, 'data')
            ->assertJsonPath('meta.per_page', 15)
            ->assertJsonPath('meta.total', 20)
            ->assertJsonPath('meta.current_page', 1)
            ->assertJsonPath('meta.last_page', 2);

        $this->getJson('/api/trips?page=2')
            ->assertOk()
            ->assertJsonCount(5, 'data')
            ->assertJsonPath('meta.current_page', 2);
    }

    public function test_trips_endpoint_has_no_n_plus_one_queries(): void
    {
        Sanctum::actingAs(User::factory()->create());

        // 18 viagens (mais que o per_page), cada uma com seu veículo e motorista.
        Trip::factory()->count(18)->create();

        DB::enableQueryLog();
        $this->getJson('/api/trips')->assertOk();
        $queries = DB::getQueryLog();
        DB::disableQueryLog();

        // Com paginação + eager loading: count + trips + vehicles + drivers = 4 queries,
        // independente da quantidade (sem N+1).
        $this->assertLessThanOrEqual(4, count($queries));
    }

    public function test_without_filters_returns_all_paginated(): void
    {
        Sanctum::actingAs(User::factory()->create());
        Trip::factory()->count(3)->create();

        $this->getJson('/api/trips')
            ->assertOk()
            ->assertJsonCount(3, 'data')
            ->assertJsonPath('meta.total', 3);
    }

    public function test_can_filter_by_destination(): void
    {
        Sanctum::actingAs(User::factory()->create());

        Trip::factory()->create(['destination' => 'Gramado']);
        Trip::factory()->create(['destination' => 'Torres']);

        $this->getJson('/api/trips?destination=Gramado')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.destination', 'Gramado');
    }

    public function test_can_filter_by_departure_date(): void
    {
        Sanctum::actingAs(User::factory()->create());

        Trip::factory()->create(['departure_at' => '2030-06-20 08:00', 'arrival_at' => '2030-06-20 12:00']);
        Trip::factory()->create(['departure_at' => '2030-06-10 08:00', 'arrival_at' => '2030-06-10 12:00']);

        // A partir de 15/06 → só a viagem de 20/06.
        $this->getJson('/api/trips?date=2030-06-15')
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_status_is_included_in_the_response(): void
    {
        Sanctum::actingAs(User::factory()->create());
        Trip::factory()->create(['status' => TripStatus::Concluida]);

        $this->getJson('/api/trips')
            ->assertOk()
            ->assertJsonPath('data.0.status', 'concluida');
    }

    public function test_user_endpoint_uses_resource_and_hides_password(): void
    {
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/user');

        $response->assertOk()
            ->assertJsonStructure(['data' => ['id', 'name', 'email']])
            ->assertJsonPath('data.email', $user->email);

        $this->assertArrayNotHasKey('password', $response->json('data'));
    }
}
