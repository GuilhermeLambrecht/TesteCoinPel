<?php

namespace Tests\Feature\Contracts;

use App\Models\Client;
use App\Models\Contract;
use App\Models\Package;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ContractCrudTest extends TestCase
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
        $client = Client::factory()->create(['active' => true]);
        $package = Package::factory()->create(['active' => true]);

        return array_merge([
            'title' => 'Contrato Serra Gaúcha',
            'client_id' => $client->id,
            'package_id' => $package->id,
            'start_date' => '2030-06-01',
            'end_date' => '2030-06-10',
            'value' => 5000.00,
        ], $overrides);
    }

    public function test_guest_cannot_access_contracts(): void
    {
        $this->get('/contracts')->assertRedirect('/login');
        $this->post('/contracts', $this->validData())->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_contracts_list(): void
    {
        Contract::factory()->create(['title' => 'Contrato Litoral']);

        $this->actingAs($this->admin())
            ->get('/contracts')
            ->assertOk()
            ->assertSee('Contrato Litoral');
    }

    public function test_contract_can_be_created(): void
    {
        $data = $this->validData();

        $response = $this->actingAs($this->admin())->post('/contracts', $data);

        $response->assertRedirect('/contracts');
        $response->assertSessionHas('status');
        $this->assertDatabaseHas('contracts', [
            'title' => 'Contrato Serra Gaúcha',
            'client_id' => $data['client_id'],
            'package_id' => $data['package_id'],
        ]);
    }

    public function test_contract_can_be_updated(): void
    {
        $contract = Contract::factory()->create(['title' => 'Antigo']);

        $response = $this->actingAs($this->admin())
            ->put("/contracts/{$contract->id}", $this->validData([
                'title' => 'Contrato Novo',
            ]));

        $response->assertRedirect('/contracts');
        $this->assertDatabaseHas('contracts', [
            'id' => $contract->id,
            'title' => 'Contrato Novo',
        ]);
    }

    public function test_contract_can_be_soft_deleted(): void
    {
        $contract = Contract::factory()->create();

        $response = $this->actingAs($this->admin())
            ->delete("/contracts/{$contract->id}");

        $response->assertRedirect('/contracts');
        $this->assertSoftDeleted('contracts', ['id' => $contract->id]);
    }

    public function test_validation_requires_mandatory_fields(): void
    {
        $this->actingAs($this->admin())
            ->from('/contracts/create')
            ->post('/contracts', [])
            ->assertSessionHasErrors(['title', 'client_id', 'package_id', 'start_date', 'end_date', 'value']);
    }

    public function test_client_is_required(): void
    {
        $this->actingAs($this->admin())
            ->from('/contracts/create')
            ->post('/contracts', $this->validData(['client_id' => null]))
            ->assertRedirect('/contracts/create')
            ->assertSessionHasErrors('client_id');

        $this->assertDatabaseCount('contracts', 0);
    }

    public function test_client_must_exist(): void
    {
        $this->actingAs($this->admin())
            ->from('/contracts/create')
            ->post('/contracts', $this->validData(['client_id' => 999999]))
            ->assertRedirect('/contracts/create')
            ->assertSessionHasErrors('client_id');
    }

    public function test_inactive_client_is_rejected_on_create(): void
    {
        $client = Client::factory()->create(['active' => false]);

        $this->actingAs($this->admin())
            ->from('/contracts/create')
            ->post('/contracts', $this->validData(['client_id' => $client->id]))
            ->assertSessionHasErrors('client_id');
    }

    public function test_contract_edit_keeps_currently_linked_inactive_client(): void
    {
        $client = Client::factory()->create(['active' => true]);
        $contract = Contract::factory()->create(['client_id' => $client->id]);

        // O cliente é desativado DEPOIS de vinculado.
        $client->update(['active' => false]);

        $this->actingAs($this->admin())
            ->put("/contracts/{$contract->id}", $this->validData([
                'client_id' => $client->id,
            ]))
            ->assertRedirect('/contracts')
            ->assertSessionHasNoErrors();
    }

    public function test_client_has_many_contracts_relationship(): void
    {
        $client = Client::factory()->create();
        Contract::factory()->count(3)->create(['client_id' => $client->id]);
        Contract::factory()->create(); // de outro cliente

        $this->assertCount(3, $client->contracts);
        $this->assertSame(3, $client->contracts()->count());
    }

    public function test_contracts_listing_shows_client_without_n_plus_one(): void
    {
        $admin = $this->admin();

        // 12 contratos, cada um com seu cliente e pacote (> per_page).
        Contract::factory()->count(12)->create();

        // Contrato mais recente → garantido na 1ª página (orderByDesc('id')).
        $client = Client::factory()->create(['name' => 'Cliente Em Destaque']);
        Contract::factory()->create(['client_id' => $client->id]);

        DB::enableQueryLog();
        $response = $this->actingAs($admin)->get('/contracts');
        $queries = DB::getQueryLog();
        DB::disableQueryLog();

        $response->assertOk()->assertSee('Cliente Em Destaque');

        // Com eager loading de client e package, o nº de queries é constante
        // (paginação + contratos + clientes + pacotes), sem crescer por linha.
        // Sem eager loading, as ~10 linhas dobrariam as queries.
        $this->assertLessThanOrEqual(6, count($queries));
    }

    public function test_end_date_must_not_be_before_start_date(): void
    {
        $this->actingAs($this->admin())
            ->from('/contracts/create')
            ->post('/contracts', $this->validData([
                'start_date' => '2030-06-10',
                'end_date' => '2030-06-01',
            ]))
            ->assertRedirect('/contracts/create')
            ->assertSessionHasErrors('end_date');

        $this->assertDatabaseCount('contracts', 0);
    }

    public function test_end_date_equal_to_start_date_is_allowed(): void
    {
        $this->actingAs($this->admin())
            ->post('/contracts', $this->validData([
                'start_date' => '2030-06-01',
                'end_date' => '2030-06-01',
            ]))
            ->assertRedirect('/contracts')
            ->assertSessionHasNoErrors();
    }

    public function test_contract_can_be_created_with_a_valid_status(): void
    {
        $this->actingAs($this->admin())
            ->post('/contracts', $this->validData(['status' => 'concluido']))
            ->assertRedirect('/contracts');

        $this->assertDatabaseHas('contracts', ['title' => 'Contrato Serra Gaúcha', 'status' => 'concluido']);
    }

    public function test_contract_status_must_be_valid(): void
    {
        $this->actingAs($this->admin())
            ->from('/contracts/create')
            ->post('/contracts', $this->validData(['status' => 'banana']))
            ->assertRedirect('/contracts/create')
            ->assertSessionHasErrors('status');
    }

    public function test_contract_status_defaults_to_rascunho_when_omitted(): void
    {
        // validData() não inclui status → usa o default do banco.
        $this->actingAs($this->admin())
            ->post('/contracts', $this->validData())
            ->assertRedirect('/contracts');

        $this->assertDatabaseHas('contracts', ['title' => 'Contrato Serra Gaúcha', 'status' => 'rascunho']);
    }

    public function test_package_must_exist(): void
    {
        $this->actingAs($this->admin())
            ->from('/contracts/create')
            ->post('/contracts', $this->validData(['package_id' => 999999]))
            ->assertRedirect('/contracts/create')
            ->assertSessionHasErrors('package_id');
    }

    public function test_inactive_package_is_rejected_on_create(): void
    {
        $package = Package::factory()->create(['active' => false]);

        $this->actingAs($this->admin())
            ->from('/contracts/create')
            ->post('/contracts', $this->validData(['package_id' => $package->id]))
            ->assertSessionHasErrors('package_id');
    }

    public function test_contract_edit_keeps_currently_linked_inactive_package(): void
    {
        $package = Package::factory()->create(['active' => true]);
        $contract = Contract::factory()->create(['package_id' => $package->id]);

        // O pacote é desativado DEPOIS de vinculado.
        $package->update(['active' => false]);

        $this->actingAs($this->admin())
            ->put("/contracts/{$contract->id}", $this->validData([
                'package_id' => $package->id,
            ]))
            ->assertRedirect('/contracts')
            ->assertSessionHasNoErrors();
    }

    public function test_contract_edit_form_shows_current_inactive_package_marked(): void
    {
        $package = Package::factory()->create(['active' => true, 'name' => 'Pacote Vinculado']);
        $contract = Contract::factory()->create(['package_id' => $package->id]);

        $package->update(['active' => false]);

        $this->actingAs($this->admin())
            ->get("/contracts/{$contract->id}/edit")
            ->assertOk()
            ->assertSee('Pacote Vinculado')
            ->assertSee('(inativo)');
    }

    public function test_contract_edit_rejects_a_different_inactive_package(): void
    {
        $package = Package::factory()->create(['active' => true]);
        $contract = Contract::factory()->create(['package_id' => $package->id]);

        $otherInactive = Package::factory()->create(['active' => false]);

        $this->actingAs($this->admin())
            ->from("/contracts/{$contract->id}/edit")
            ->put("/contracts/{$contract->id}", $this->validData([
                'package_id' => $otherInactive->id,
            ]))
            ->assertRedirect("/contracts/{$contract->id}/edit")
            ->assertSessionHasErrors('package_id');
    }

    public function test_list_can_be_searched_by_title(): void
    {
        Contract::factory()->create(['title' => 'Contrato Encontrado']);
        Contract::factory()->create(['title' => 'Contrato Oculto']);

        $this->actingAs($this->admin())
            ->get('/contracts?search=encontrado')
            ->assertOk()
            ->assertSee('Contrato Encontrado')
            ->assertDontSee('Contrato Oculto');
    }

    public function test_list_is_paginated(): void
    {
        Contract::factory()->count(15)->create();

        $response = $this->actingAs($this->admin())->get('/contracts');

        $response->assertOk();
        $contracts = $response->viewData('contracts');
        $this->assertSame(10, $contracts->count());
        $this->assertSame(15, $contracts->total());
        $this->assertSame(2, $contracts->lastPage());
    }
}
