<?php

namespace Tests\Feature\Packages;

use App\Models\Package;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PackageCrudTest extends TestCase
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
            'name' => 'Serra Gaúcha',
            'destination' => 'Gramado',
            'duration_days' => 5,
            'price' => 1499.90,
            'description' => 'Passeio pela serra com hospedagem incluída.',
            'active' => '1',
        ], $overrides);
    }

    public function test_guest_cannot_access_packages(): void
    {
        $this->get('/packages')->assertRedirect('/login');
        $this->post('/packages', $this->validData())->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_packages_list(): void
    {
        Package::factory()->create(['name' => 'Pacote Litoral Sul']);

        $this->actingAs($this->admin())
            ->get('/packages')
            ->assertOk()
            ->assertSee('Pacote Litoral Sul');
    }

    public function test_package_can_be_created(): void
    {
        $response = $this->actingAs($this->admin())
            ->post('/packages', $this->validData());

        $response->assertRedirect('/packages');
        $response->assertSessionHas('status');
        $this->assertDatabaseHas('packages', [
            'name' => 'Serra Gaúcha',
            'destination' => 'Gramado',
            'duration_days' => 5,
        ]);
    }

    public function test_description_is_optional(): void
    {
        $this->actingAs($this->admin())
            ->post('/packages', $this->validData(['description' => null]))
            ->assertRedirect('/packages')
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas('packages', [
            'name' => 'Serra Gaúcha',
            'description' => null,
        ]);
    }

    public function test_package_can_be_updated(): void
    {
        $package = Package::factory()->create(['name' => 'Antigo', 'destination' => 'Velho Destino']);

        $response = $this->actingAs($this->admin())
            ->put("/packages/{$package->id}", $this->validData([
                'name' => 'Pacote Novo',
                'destination' => 'Bombinhas',
            ]));

        $response->assertRedirect('/packages');
        $this->assertDatabaseHas('packages', [
            'id' => $package->id,
            'name' => 'Pacote Novo',
            'destination' => 'Bombinhas',
        ]);
    }

    public function test_package_can_be_soft_deleted(): void
    {
        $package = Package::factory()->create();

        $response = $this->actingAs($this->admin())
            ->delete("/packages/{$package->id}");

        $response->assertRedirect('/packages');
        $this->assertSoftDeleted('packages', ['id' => $package->id]);
    }

    public function test_validation_requires_mandatory_fields(): void
    {
        $this->actingAs($this->admin())
            ->from('/packages/create')
            ->post('/packages', [])
            ->assertSessionHasErrors(['name', 'destination', 'duration_days', 'price']);
    }

    public function test_price_and_duration_must_be_positive(): void
    {
        $this->actingAs($this->admin())
            ->from('/packages/create')
            ->post('/packages', $this->validData(['price' => 0, 'duration_days' => 0]))
            ->assertRedirect('/packages/create')
            ->assertSessionHasErrors(['price', 'duration_days']);

        $this->assertDatabaseCount('packages', 0);
    }

    public function test_list_can_be_searched_by_name_or_destination(): void
    {
        Package::factory()->create(['name' => 'Pacote Encontrado', 'destination' => 'Canela']);
        Package::factory()->create(['name' => 'Pacote Oculto', 'destination' => 'Torres']);

        $admin = $this->admin();

        // Busca por nome
        $this->actingAs($admin)
            ->get('/packages?search=encontrado')
            ->assertOk()
            ->assertSee('Pacote Encontrado')
            ->assertDontSee('Pacote Oculto');

        // Busca por destino
        $this->actingAs($admin)
            ->get('/packages?search=torres')
            ->assertOk()
            ->assertSee('Pacote Oculto')
            ->assertDontSee('Pacote Encontrado');
    }

    public function test_list_is_paginated(): void
    {
        Package::factory()->count(15)->create();

        $response = $this->actingAs($this->admin())->get('/packages');

        $response->assertOk();
        $packages = $response->viewData('packages');
        $this->assertSame(10, $packages->count());
        $this->assertSame(15, $packages->total());
        $this->assertSame(2, $packages->lastPage());
    }
}
