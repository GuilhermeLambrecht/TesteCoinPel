<?php

namespace Tests\Feature;

use App\Models\Driver;
use App\Models\Trip;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_dashboard_shows_summary_counters(): void
    {
        $admin = User::factory()->create(['must_change_password' => false]);

        $vehicles = Vehicle::factory()->count(3)->create();
        $drivers = Driver::factory()->count(2)->create();

        // Reaproveita veículos/motoristas (recycle) p/ as viagens não inflarem as contagens.
        Trip::factory()->count(3)->recycle([$vehicles, $drivers])->create([
            'departure_at' => now()->addWeek(),
            'arrival_at' => now()->addWeek()->addHours(3),
        ]);
        Trip::factory()->recycle([$vehicles, $drivers])->create([
            'departure_at' => now()->subWeek(),
            'arrival_at' => now()->subWeek()->addHours(3),
        ]);

        $response = $this->actingAs($admin)->get('/dashboard');

        $response->assertOk()
            ->assertViewHas('tripsCount', 4)
            ->assertViewHas('vehiclesCount', 3)
            ->assertViewHas('driversCount', 2)
            ->assertViewHas('usersCount', 1)
            ->assertViewHas('upcomingTripsCount', 3)
            ->assertSee('Viagens')
            ->assertSee('Veículos')
            ->assertSee('Motoristas')
            ->assertSee('Usuários')
            ->assertSee('Próximas viagens');
    }

    public function test_dashboard_lists_only_future_trips(): void
    {
        $admin = User::factory()->create(['must_change_password' => false]);
        $vehicle = Vehicle::factory()->create();
        $driver = Driver::factory()->create();

        Trip::factory()->recycle([$vehicle, $driver])->create([
            'origin' => 'Pelotas', 'destination' => 'Gramado',
            'departure_at' => now()->addDays(3), 'arrival_at' => now()->addDays(3)->addHours(3),
        ]);
        Trip::factory()->recycle([$vehicle, $driver])->create([
            'origin' => 'ViagemPassada', 'destination' => 'Antiga',
            'departure_at' => now()->subDays(3), 'arrival_at' => now()->subDays(3)->addHours(3),
        ]);

        $this->actingAs($admin)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Pelotas → Gramado')
            ->assertDontSee('ViagemPassada');
    }

    public function test_upcoming_trips_are_ordered_by_departure(): void
    {
        $admin = User::factory()->create(['must_change_password' => false]);
        $vehicle = Vehicle::factory()->create();
        $driver = Driver::factory()->create();

        // Criadas fora de ordem; devem aparecer da mais próxima para a mais distante.
        Trip::factory()->recycle([$vehicle, $driver])->create([
            'origin' => 'Distante', 'destination' => 'X',
            'departure_at' => now()->addDays(10), 'arrival_at' => now()->addDays(10)->addHours(2),
        ]);
        Trip::factory()->recycle([$vehicle, $driver])->create([
            'origin' => 'Proxima', 'destination' => 'X',
            'departure_at' => now()->addDays(2), 'arrival_at' => now()->addDays(2)->addHours(2),
        ]);
        Trip::factory()->recycle([$vehicle, $driver])->create([
            'origin' => 'Intermediaria', 'destination' => 'X',
            'departure_at' => now()->addDays(5), 'arrival_at' => now()->addDays(5)->addHours(2),
        ]);

        $this->actingAs($admin)
            ->get('/dashboard')
            ->assertSeeInOrder(['Proxima', 'Intermediaria', 'Distante']);
    }

    public function test_dashboard_shows_empty_state_without_upcoming_trips(): void
    {
        $admin = User::factory()->create(['must_change_password' => false]);

        // Apenas uma viagem no passado → nenhuma futura.
        Trip::factory()->create([
            'departure_at' => now()->subWeek(), 'arrival_at' => now()->subWeek()->addHours(2),
        ]);

        $this->actingAs($admin)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Nenhuma viagem agendada');
    }

    public function test_upcoming_trips_avoid_n_plus_one(): void
    {
        $admin = User::factory()->create(['must_change_password' => false]);

        // 5 viagens futuras, cada uma com seu próprio veículo e motorista.
        for ($i = 1; $i <= 5; $i++) {
            Trip::factory()->create([
                'departure_at' => now()->addDays($i),
                'arrival_at' => now()->addDays($i)->addHours(2),
            ]);
        }

        DB::enableQueryLog();
        $this->actingAs($admin)->get('/dashboard')->assertOk();
        $queries = count(DB::getQueryLog());
        DB::disableQueryLog();

        // 5 contadores (COUNT) + próximas viagens (1) + vehicles (1) + drivers (1) = 8.
        // Com eager loading o número não cresce com a quantidade de viagens.
        $this->assertLessThanOrEqual(8, $queries);
    }
}
