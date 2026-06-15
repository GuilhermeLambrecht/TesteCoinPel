<?php

namespace Tests\Feature\Trips;

use App\Models\Driver;
use App\Models\Trip;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TripCrudTest extends TestCase
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
        $vehicle = Vehicle::factory()->create(['active' => true]);
        $driver = Driver::factory()->create(['active' => true]);

        return array_merge([
            'origin' => 'Pelotas',
            'destination' => 'Gramado',
            'departure_at' => '2030-06-20T08:00',
            'arrival_at' => '2030-06-20T14:30',
            'vehicle_id' => $vehicle->id,
            'driver_id' => $driver->id,
        ], $overrides);
    }

    public function test_guest_cannot_access_trips(): void
    {
        $this->get('/trips')->assertRedirect('/login');
        $this->post('/trips', $this->validData())->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_trips_list(): void
    {
        Trip::factory()->create(['origin' => 'Capão da Canoa']);

        $this->actingAs($this->admin())
            ->get('/trips')
            ->assertOk()
            ->assertSee('Capão da Canoa');
    }

    public function test_trip_can_be_created(): void
    {
        $data = $this->validData();

        $response = $this->actingAs($this->admin())->post('/trips', $data);

        $response->assertRedirect('/trips');
        $response->assertSessionHas('status');
        $this->assertDatabaseHas('trips', [
            'origin' => 'Pelotas',
            'destination' => 'Gramado',
            'vehicle_id' => $data['vehicle_id'],
            'driver_id' => $data['driver_id'],
        ]);
    }

    public function test_arrival_must_be_after_departure(): void
    {
        $this->actingAs($this->admin())
            ->from('/trips/create')
            ->post('/trips', $this->validData([
                'departure_at' => '2030-06-20T14:00',
                'arrival_at' => '2030-06-20T09:00',
            ]))
            ->assertRedirect('/trips/create')
            ->assertSessionHasErrors('arrival_at');

        $this->assertDatabaseCount('trips', 0);
    }

    public function test_inactive_vehicle_is_rejected(): void
    {
        $vehicle = Vehicle::factory()->create(['active' => false]);

        $this->actingAs($this->admin())
            ->from('/trips/create')
            ->post('/trips', $this->validData(['vehicle_id' => $vehicle->id]))
            ->assertSessionHasErrors('vehicle_id');
    }

    public function test_trip_can_be_updated(): void
    {
        $trip = Trip::factory()->create();

        $response = $this->actingAs($this->admin())
            ->put("/trips/{$trip->id}", $this->validData([
                'origin' => 'Rio Grande',
                'destination' => 'Porto Alegre',
            ]));

        $response->assertRedirect('/trips');
        $this->assertDatabaseHas('trips', [
            'id' => $trip->id,
            'origin' => 'Rio Grande',
            'destination' => 'Porto Alegre',
        ]);
    }

    public function test_trip_can_be_soft_deleted(): void
    {
        $trip = Trip::factory()->create();

        $response = $this->actingAs($this->admin())
            ->delete("/trips/{$trip->id}");

        $response->assertRedirect('/trips');
        $this->assertSoftDeleted('trips', ['id' => $trip->id]);
    }

    public function test_validation_requires_mandatory_fields(): void
    {
        $this->actingAs($this->admin())
            ->from('/trips/create')
            ->post('/trips', [])
            ->assertSessionHasErrors(['origin', 'destination', 'departure_at', 'arrival_at', 'vehicle_id', 'driver_id']);
    }

    public function test_list_can_be_searched_by_origin_or_destination(): void
    {
        Trip::factory()->create(['origin' => 'Santa Maria', 'destination' => 'Uruguaiana']);
        Trip::factory()->create(['origin' => 'Canela', 'destination' => 'Torres']);

        $admin = $this->admin();

        // Busca por origem
        $this->actingAs($admin)
            ->get('/trips?search=santa')
            ->assertOk()
            ->assertSee('Santa Maria')
            ->assertDontSee('Canela');

        // Busca por destino
        $this->actingAs($admin)
            ->get('/trips?search=torres')
            ->assertOk()
            ->assertSee('Canela')
            ->assertDontSee('Santa Maria');
    }

    public function test_list_is_paginated(): void
    {
        Trip::factory()->count(15)->create();

        $response = $this->actingAs($this->admin())->get('/trips');

        $response->assertOk();
        $trips = $response->viewData('trips');
        $this->assertSame(10, $trips->count());
        $this->assertSame(15, $trips->total());
        $this->assertSame(2, $trips->lastPage());
    }

    public function test_trip_edit_keeps_currently_linked_inactive_records(): void
    {
        $vehicle = Vehicle::factory()->create(['active' => true]);
        $driver = Driver::factory()->create(['active' => true]);
        $trip = Trip::factory()->create(['vehicle_id' => $vehicle->id, 'driver_id' => $driver->id]);

        // Veículo e motorista são desativados DEPOIS de vinculados.
        $vehicle->update(['active' => false]);
        $driver->update(['active' => false]);

        $this->actingAs($this->admin())
            ->put("/trips/{$trip->id}", [
                'origin' => 'Pelotas',
                'destination' => 'Gramado',
                'departure_at' => '2030-06-20T08:00',
                'arrival_at' => '2030-06-20T12:00',
                'vehicle_id' => $vehicle->id,
                'driver_id' => $driver->id,
            ])
            ->assertRedirect('/trips')
            ->assertSessionHasNoErrors();
    }

    public function test_trip_edit_form_shows_current_inactive_vehicle_marked(): void
    {
        $vehicle = Vehicle::factory()->create(['active' => true, 'plate' => 'OLD1A11']);
        $driver = Driver::factory()->create(['active' => true]);
        $trip = Trip::factory()->create(['vehicle_id' => $vehicle->id, 'driver_id' => $driver->id]);

        $vehicle->update(['active' => false]);

        $this->actingAs($this->admin())
            ->get("/trips/{$trip->id}/edit")
            ->assertOk()
            ->assertSee('OLD1A11')
            ->assertSee('(inativo)');
    }

    public function test_trip_edit_rejects_a_different_inactive_vehicle(): void
    {
        $vehicle = Vehicle::factory()->create(['active' => true]);
        $driver = Driver::factory()->create(['active' => true]);
        $trip = Trip::factory()->create(['vehicle_id' => $vehicle->id, 'driver_id' => $driver->id]);

        $otherInactive = Vehicle::factory()->create(['active' => false]);

        $this->actingAs($this->admin())
            ->from("/trips/{$trip->id}/edit")
            ->put("/trips/{$trip->id}", [
                'origin' => 'Pelotas',
                'destination' => 'Gramado',
                'departure_at' => '2030-06-20T08:00',
                'arrival_at' => '2030-06-20T12:00',
                'vehicle_id' => $otherInactive->id, // inativo diferente do atual → rejeita
                'driver_id' => $driver->id,
            ])
            ->assertRedirect("/trips/{$trip->id}/edit")
            ->assertSessionHasErrors('vehicle_id');
    }

    public function test_validation_messages_are_in_portuguese(): void
    {
        $this->actingAs($this->admin())
            ->from('/trips/create')
            ->post('/trips', [])
            ->assertSessionHasErrors('origin');

        // A mensagem padrão de "required" deve vir em português (lang/pt_BR).
        $this->assertStringContainsString('obrigatório', session('errors')->first('origin'));
    }

    /**
     * Cria uma viagem existente ocupando 20/06/2030 08:00–12:00.
     */
    private function busyTrip(int $vehicleId, int $driverId): Trip
    {
        return Trip::factory()->create([
            'vehicle_id' => $vehicleId,
            'driver_id' => $driverId,
            'departure_at' => '2030-06-20 08:00',
            'arrival_at' => '2030-06-20 12:00',
        ]);
    }

    public function test_create_is_blocked_when_vehicle_overlaps(): void
    {
        $vehicle = Vehicle::factory()->create(['active' => true]);
        $this->busyTrip($vehicle->id, Driver::factory()->create(['active' => true])->id);
        $freeDriver = Driver::factory()->create(['active' => true]);

        $this->actingAs($this->admin())
            ->from('/trips/create')
            ->post('/trips', [
                'origin' => 'Pelotas', 'destination' => 'Gramado',
                'departure_at' => '2030-06-20T10:00', 'arrival_at' => '2030-06-20T14:00',
                'vehicle_id' => $vehicle->id, 'driver_id' => $freeDriver->id,
            ])
            ->assertSessionHasErrors('vehicle_id');
    }

    public function test_create_is_blocked_when_driver_overlaps(): void
    {
        $driver = Driver::factory()->create(['active' => true]);
        $this->busyTrip(Vehicle::factory()->create(['active' => true])->id, $driver->id);
        $freeVehicle = Vehicle::factory()->create(['active' => true]);

        $this->actingAs($this->admin())
            ->from('/trips/create')
            ->post('/trips', [
                'origin' => 'Pelotas', 'destination' => 'Gramado',
                'departure_at' => '2030-06-20T10:00', 'arrival_at' => '2030-06-20T14:00',
                'vehicle_id' => $freeVehicle->id, 'driver_id' => $driver->id,
            ])
            ->assertSessionHasErrors('driver_id');
    }

    public function test_touching_schedules_are_allowed(): void
    {
        $vehicle = Vehicle::factory()->create(['active' => true]);
        $driver = Driver::factory()->create(['active' => true]);
        $this->busyTrip($vehicle->id, $driver->id);

        // Começa exatamente quando a outra termina (12:00) → não conflita.
        $this->actingAs($this->admin())
            ->post('/trips', [
                'origin' => 'Pelotas', 'destination' => 'Gramado',
                'departure_at' => '2030-06-20T12:00', 'arrival_at' => '2030-06-20T16:00',
                'vehicle_id' => $vehicle->id, 'driver_id' => $driver->id,
            ])
            ->assertRedirect('/trips')
            ->assertSessionHasNoErrors();
    }

    public function test_non_overlapping_schedule_is_allowed(): void
    {
        $vehicle = Vehicle::factory()->create(['active' => true]);
        $driver = Driver::factory()->create(['active' => true]);
        $this->busyTrip($vehicle->id, $driver->id);

        // Outro dia, mesmo veículo e motorista → sem conflito.
        $this->actingAs($this->admin())
            ->post('/trips', [
                'origin' => 'Pelotas', 'destination' => 'Gramado',
                'departure_at' => '2030-06-21T08:00', 'arrival_at' => '2030-06-21T12:00',
                'vehicle_id' => $vehicle->id, 'driver_id' => $driver->id,
            ])
            ->assertRedirect('/trips')
            ->assertSessionHasNoErrors();
    }

    public function test_editing_trip_does_not_conflict_with_itself(): void
    {
        $vehicle = Vehicle::factory()->create(['active' => true]);
        $driver = Driver::factory()->create(['active' => true]);
        $trip = $this->busyTrip($vehicle->id, $driver->id);

        // Mantém os próprios horários/veículo/motorista → não acusa conflito consigo.
        $this->actingAs($this->admin())
            ->put("/trips/{$trip->id}", [
                'origin' => 'Pelotas', 'destination' => 'Gramado',
                'departure_at' => '2030-06-20T08:00', 'arrival_at' => '2030-06-20T12:00',
                'vehicle_id' => $vehicle->id, 'driver_id' => $driver->id,
            ])
            ->assertRedirect('/trips')
            ->assertSessionHasNoErrors();
    }

    public function test_editing_into_overlap_with_another_trip_is_blocked(): void
    {
        $vehicle = Vehicle::factory()->create(['active' => true]);
        $driver = Driver::factory()->create(['active' => true]);
        $this->busyTrip($vehicle->id, $driver->id); // ocupa 08:00–12:00

        $other = Trip::factory()->create([
            'vehicle_id' => $vehicle->id, 'driver_id' => $driver->id,
            'departure_at' => '2030-06-25 08:00', 'arrival_at' => '2030-06-25 12:00',
        ]);

        // Edita a segunda viagem para sobrepor a primeira.
        $this->actingAs($this->admin())
            ->from("/trips/{$other->id}/edit")
            ->put("/trips/{$other->id}", [
                'origin' => 'Pelotas', 'destination' => 'Gramado',
                'departure_at' => '2030-06-20T10:00', 'arrival_at' => '2030-06-20T14:00',
                'vehicle_id' => $vehicle->id, 'driver_id' => $driver->id,
            ])
            ->assertSessionHasErrors('vehicle_id');
    }

    public function test_simultaneous_vehicle_and_driver_conflict_is_blocked(): void
    {
        $vehicle = Vehicle::factory()->create(['active' => true]);
        $driver = Driver::factory()->create(['active' => true]);
        $this->busyTrip($vehicle->id, $driver->id);

        $this->actingAs($this->admin())
            ->from('/trips/create')
            ->post('/trips', [
                'origin' => 'Pelotas', 'destination' => 'Gramado',
                'departure_at' => '2030-06-20T10:00', 'arrival_at' => '2030-06-20T14:00',
                'vehicle_id' => $vehicle->id, 'driver_id' => $driver->id,
            ])
            ->assertSessionHasErrors(['vehicle_id', 'driver_id']);
    }

    public function test_list_can_be_filtered_by_departure_date(): void
    {
        $vehicle = Vehicle::factory()->create(['active' => true]);
        $driver = Driver::factory()->create(['active' => true]);

        Trip::factory()->recycle([$vehicle, $driver])->create([
            'origin' => 'ViagemRecente', 'destination' => 'X',
            'departure_at' => '2030-06-20 08:00', 'arrival_at' => '2030-06-20 12:00',
        ]);
        Trip::factory()->recycle([$vehicle, $driver])->create([
            'origin' => 'ViagemAntiga', 'destination' => 'Y',
            'departure_at' => '2030-06-10 08:00', 'arrival_at' => '2030-06-10 12:00',
        ]);

        // A partir de 15/06 → só a viagem de 20/06 aparece.
        $this->actingAs($this->admin())
            ->get('/trips?date=2030-06-15')
            ->assertOk()
            ->assertSee('ViagemRecente')
            ->assertDontSee('ViagemAntiga');
    }

    public function test_trip_can_be_created_with_a_valid_status(): void
    {
        $this->actingAs($this->admin())
            ->post('/trips', $this->validData(['status' => 'concluida']))
            ->assertRedirect('/trips');

        $this->assertDatabaseHas('trips', ['origin' => 'Pelotas', 'status' => 'concluida']);
    }

    public function test_trip_status_must_be_valid(): void
    {
        $this->actingAs($this->admin())
            ->from('/trips/create')
            ->post('/trips', $this->validData(['status' => 'banana']))
            ->assertRedirect('/trips/create')
            ->assertSessionHasErrors('status');
    }

    public function test_trip_status_defaults_to_agendada_when_omitted(): void
    {
        // validData() não inclui status → usa o default do banco.
        $this->actingAs($this->admin())
            ->post('/trips', $this->validData())
            ->assertRedirect('/trips');

        $this->assertDatabaseHas('trips', ['origin' => 'Pelotas', 'status' => 'agendada']);
    }
}
