<?php

namespace Tests\Feature;

use App\Models\Contract;
use App\Models\Driver;
use App\Models\Package;
use App\Models\Trip;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Tests\TestCase;

class StatisticsTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['must_change_password' => false]);
    }

    public function test_guest_cannot_access_statistics(): void
    {
        $this->get('/statistics')->assertRedirect('/login');
    }

    public function test_statistics_page_shows_correct_numbers(): void
    {
        // Veículos: 2 ativos + 1 inativo (total 3).
        $vehicles = Vehicle::factory()->count(2)->create(['active' => true]);
        Vehicle::factory()->create(['active' => false]);

        // Motoristas: 1 ativo + 1 inativo (total 2).
        $driver = Driver::factory()->create(['active' => true]);
        Driver::factory()->create(['active' => false]);

        // Viagens: recicla veículos/motoristas existentes (não cria novos).
        Trip::factory()->recycle([$vehicles, $driver])->create(['status' => 'agendada']);
        Trip::factory()->recycle([$vehicles, $driver])->create(['status' => 'agendada']);
        Trip::factory()->recycle([$vehicles, $driver])->create(['status' => 'concluida']);

        // Pacotes: 2 ativos + 1 inativo (total 3).
        $packages = Package::factory()->count(2)->create(['active' => true]);
        Package::factory()->create(['active' => false]);

        // Contratos: recicla pacotes (não cria novos); 2 ativos somando 1500 + 1 rascunho.
        Contract::factory()->recycle($packages)->create(['status' => 'ativo', 'value' => 1000]);
        Contract::factory()->recycle($packages)->create(['status' => 'ativo', 'value' => 500]);
        Contract::factory()->recycle($packages)->create(['status' => 'rascunho', 'value' => 9999]);

        $response = $this->actingAs($this->admin())->get('/statistics');

        $response->assertOk()->assertSee('Estatísticas');

        $this->assertSame(3, $response->viewData('tripsTotal'));
        $this->assertSame(3, $response->viewData('vehiclesTotal'));
        $this->assertSame(2, $response->viewData('vehiclesActive'));
        $this->assertSame(2, $response->viewData('driversTotal'));
        $this->assertSame(1, $response->viewData('driversActive'));
        $this->assertSame(3, $response->viewData('packagesTotal'));
        $this->assertSame(2, $response->viewData('packagesActive'));
        $this->assertSame(3, $response->viewData('contractsTotal'));
        $this->assertSame(1500.0, $response->viewData('contractsActiveValue'));

        // Contagem por status das viagens.
        $tripStatuses = collect($response->viewData('tripStatuses'));
        $this->assertSame(2, $this->countFor($tripStatuses, 'Agendada'));
        $this->assertSame(1, $this->countFor($tripStatuses, 'Concluída'));
        $this->assertSame(0, $this->countFor($tripStatuses, 'Cancelada'));

        // Contagem por status dos contratos.
        $contractStatuses = collect($response->viewData('contractStatuses'));
        $this->assertSame(2, $this->countFor($contractStatuses, 'Ativo'));
        $this->assertSame(1, $this->countFor($contractStatuses, 'Rascunho'));
        $this->assertSame(0, $this->countFor($contractStatuses, 'Cancelado'));
    }

    /**
     * @param  Collection<int, array{label: string, count: int}>  $statuses
     */
    private function countFor(Collection $statuses, string $label): int
    {
        return $statuses->firstWhere('label', $label)['count'];
    }
}
