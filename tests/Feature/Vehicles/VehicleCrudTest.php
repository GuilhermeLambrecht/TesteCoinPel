<?php

namespace Tests\Feature\Vehicles;

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VehicleCrudTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['must_change_password' => false]);
    }

    /**
     * @return array<string, mixed>
     */
    private function validData(array $overrides = []): array
    {
        return array_merge([
            'plate' => 'ABC1D23',
            'model' => 'Paradiso G7',
            'brand' => 'Marcopolo',
            'capacity' => 46,
            'year' => 2022,
            'active' => '1',
        ], $overrides);
    }

    public function test_guest_cannot_access_vehicles(): void
    {
        $this->get('/vehicles')->assertRedirect('/login');
        $this->post('/vehicles', $this->validData())->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_vehicles_list(): void
    {
        $vehicle = Vehicle::factory()->create(['plate' => 'LIST1A11']);

        $this->actingAs($this->admin())
            ->get('/vehicles')
            ->assertOk()
            ->assertSee('LIST1A11');
    }

    public function test_vehicle_can_be_created(): void
    {
        $response = $this->actingAs($this->admin())
            ->post('/vehicles', $this->validData());

        $response->assertRedirect('/vehicles');
        $response->assertSessionHas('status');
        $this->assertDatabaseHas('vehicles', [
            'plate' => 'ABC1D23',
            'brand' => 'Marcopolo',
            'capacity' => 46,
        ]);
    }

    public function test_vehicle_can_be_updated(): void
    {
        $vehicle = Vehicle::factory()->create(['plate' => 'OLD1A11', 'model' => 'Antigo']);

        $response = $this->actingAs($this->admin())
            ->put("/vehicles/{$vehicle->id}", $this->validData([
                'plate' => 'NEW1A11',
                'model' => 'Novo Modelo',
            ]));

        $response->assertRedirect('/vehicles');
        $this->assertDatabaseHas('vehicles', [
            'id' => $vehicle->id,
            'plate' => 'NEW1A11',
            'model' => 'Novo Modelo',
        ]);
    }

    public function test_vehicle_can_be_soft_deleted(): void
    {
        $vehicle = Vehicle::factory()->create();

        $response = $this->actingAs($this->admin())
            ->delete("/vehicles/{$vehicle->id}");

        $response->assertRedirect('/vehicles');
        $this->assertSoftDeleted('vehicles', ['id' => $vehicle->id]);
    }

    public function test_plate_must_be_unique_on_create(): void
    {
        Vehicle::factory()->create(['plate' => 'DUP1A11']);

        $response = $this->actingAs($this->admin())
            ->from('/vehicles/create')
            ->post('/vehicles', $this->validData(['plate' => 'DUP1A11']));

        $response->assertRedirect('/vehicles/create');
        $response->assertSessionHasErrors('plate');
        $this->assertDatabaseCount('vehicles', 1);
    }

    public function test_plate_unique_rule_ignores_itself_on_update(): void
    {
        $vehicle = Vehicle::factory()->create(['plate' => 'SELF1A1']);

        // Atualizar mantendo a própria placa deve passar.
        $this->actingAs($this->admin())
            ->put("/vehicles/{$vehicle->id}", $this->validData(['plate' => 'SELF1A1']))
            ->assertRedirect('/vehicles')
            ->assertSessionHasNoErrors();
    }

    public function test_plate_must_be_unique_against_other_vehicles_on_update(): void
    {
        $other = Vehicle::factory()->create(['plate' => 'OTHER11']);
        $vehicle = Vehicle::factory()->create(['plate' => 'MINE111']);

        $this->actingAs($this->admin())
            ->from("/vehicles/{$vehicle->id}/edit")
            ->put("/vehicles/{$vehicle->id}", $this->validData(['plate' => 'OTHER11']))
            ->assertRedirect("/vehicles/{$vehicle->id}/edit")
            ->assertSessionHasErrors('plate');
    }

    public function test_validation_requires_mandatory_fields(): void
    {
        $this->actingAs($this->admin())
            ->from('/vehicles/create')
            ->post('/vehicles', [])
            ->assertSessionHasErrors(['plate', 'model', 'brand', 'capacity', 'year']);
    }

    public function test_list_can_be_searched_by_plate_or_model(): void
    {
        Vehicle::factory()->create(['plate' => 'FIND1A1', 'model' => 'Paradiso']);
        Vehicle::factory()->create(['plate' => 'HIDE1A1', 'model' => 'Audace']);

        $admin = $this->admin();

        // Busca por placa
        $this->actingAs($admin)
            ->get('/vehicles?search=find1')
            ->assertOk()
            ->assertSee('FIND1A1')
            ->assertDontSee('HIDE1A1');

        // Busca por modelo
        $this->actingAs($admin)
            ->get('/vehicles?search=audace')
            ->assertOk()
            ->assertSee('HIDE1A1')
            ->assertDontSee('FIND1A1');
    }

    public function test_list_is_paginated(): void
    {
        Vehicle::factory()->count(15)->create();

        $response = $this->actingAs($this->admin())->get('/vehicles');

        $response->assertOk();
        $vehicles = $response->viewData('vehicles');
        $this->assertSame(10, $vehicles->count());
        $this->assertSame(15, $vehicles->total());
        $this->assertSame(2, $vehicles->lastPage());
    }
}
