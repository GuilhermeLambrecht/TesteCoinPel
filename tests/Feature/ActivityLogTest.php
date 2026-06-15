<?php

namespace Tests\Feature;

use App\Models\Driver;
use App\Models\Trip;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityLogTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['must_change_password' => false]);
    }

    public function test_creating_a_trip_logs_activity(): void
    {
        // Veículo/motorista criados sem autenticação → não geram log.
        $vehicle = Vehicle::factory()->create(['active' => true]);
        $driver = Driver::factory()->create(['active' => true]);
        $admin = $this->admin();

        $this->actingAs($admin)->post('/trips', [
            'origin' => 'Pelotas', 'destination' => 'Gramado',
            'departure_at' => '2030-06-20T08:00', 'arrival_at' => '2030-06-20T12:00',
            'vehicle_id' => $vehicle->id, 'driver_id' => $driver->id,
        ])->assertRedirect('/trips');

        $trip = Trip::firstWhere('origin', 'Pelotas');

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $admin->id,
            'action' => 'created',
            'subject_type' => Trip::class,
            'subject_id' => $trip->id,
        ]);
    }

    public function test_updating_a_trip_logs_activity(): void
    {
        $vehicle = Vehicle::factory()->create(['active' => true]);
        $driver = Driver::factory()->create(['active' => true]);
        $trip = Trip::factory()->create([
            'origin' => 'Antigo', 'vehicle_id' => $vehicle->id, 'driver_id' => $driver->id,
        ]);
        $admin = $this->admin();

        $this->actingAs($admin)->put("/trips/{$trip->id}", [
            'origin' => 'Pelotas', 'destination' => 'Gramado',
            'departure_at' => '2030-06-20T08:00', 'arrival_at' => '2030-06-20T12:00',
            'vehicle_id' => $vehicle->id, 'driver_id' => $driver->id,
        ])->assertRedirect('/trips');

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $admin->id,
            'action' => 'updated',
            'subject_type' => Trip::class,
            'subject_id' => $trip->id,
        ]);
    }

    public function test_deleting_a_trip_logs_activity(): void
    {
        $trip = Trip::factory()->create();
        $admin = $this->admin();

        $this->actingAs($admin)->delete("/trips/{$trip->id}")->assertRedirect('/trips');

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $admin->id,
            'action' => 'deleted',
            'subject_type' => Trip::class,
            'subject_id' => $trip->id,
        ]);
    }

    public function test_system_actions_without_user_are_not_logged(): void
    {
        // Sem autenticação (ex.: seeder/factory) → nenhuma atividade registrada.
        Trip::factory()->create();

        $this->assertDatabaseCount('activity_logs', 0);
    }

    public function test_logs_screen_requires_authentication(): void
    {
        $this->get('/activity-logs')->assertRedirect('/login');
    }

    public function test_logs_screen_lists_entries(): void
    {
        $admin = $this->admin();

        // Gera um log criando uma viagem autenticado.
        $vehicle = Vehicle::factory()->create(['active' => true]);
        $driver = Driver::factory()->create(['active' => true]);
        $this->actingAs($admin)->post('/trips', [
            'origin' => 'Bagé', 'destination' => 'Pelotas',
            'departure_at' => '2030-07-01T08:00', 'arrival_at' => '2030-07-01T12:00',
            'vehicle_id' => $vehicle->id, 'driver_id' => $driver->id,
        ]);

        $this->actingAs($admin)
            ->get('/activity-logs')
            ->assertOk()
            ->assertSee('Bagé → Pelotas')
            ->assertSee($admin->name);
    }
}
