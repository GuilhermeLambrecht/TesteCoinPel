<?php

namespace Tests\Feature\Clients;

use App\Models\Client;
use App\Models\Contract;
use App\Models\Package;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ClientCrudTest extends TestCase
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
            'name' => 'Maria Souza',
            'email' => 'maria@example.com',
            'phone' => '(53) 99999-0000',
            'document' => '12345678901',
            'active' => '1',
        ], $overrides);
    }

    public function test_guest_cannot_access_clients(): void
    {
        $this->get('/clients')->assertRedirect('/login');
        $this->post('/clients', $this->validData())->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_clients_list(): void
    {
        Client::factory()->create(['name' => 'Cliente Visível']);

        $this->actingAs($this->admin())
            ->get('/clients')
            ->assertOk()
            ->assertSee('Cliente Visível');
    }

    public function test_client_can_be_created(): void
    {
        $response = $this->actingAs($this->admin())
            ->post('/clients', $this->validData());

        $response->assertRedirect('/clients');
        $response->assertSessionHas('status');
        $this->assertDatabaseHas('clients', [
            'name' => 'Maria Souza',
            'email' => 'maria@example.com',
            'document' => '12345678901',
        ]);
    }

    public function test_client_can_be_updated(): void
    {
        $client = Client::factory()->create(['name' => 'Antigo', 'email' => 'old@example.com']);

        $response = $this->actingAs($this->admin())
            ->put("/clients/{$client->id}", $this->validData([
                'name' => 'Nome Novo',
                'email' => 'novo@example.com',
                'document' => '99999999999',
            ]));

        $response->assertRedirect('/clients');
        $this->assertDatabaseHas('clients', [
            'id' => $client->id,
            'name' => 'Nome Novo',
            'email' => 'novo@example.com',
        ]);
    }

    public function test_client_can_be_soft_deleted(): void
    {
        $client = Client::factory()->create();

        $response = $this->actingAs($this->admin())
            ->delete("/clients/{$client->id}");

        $response->assertRedirect('/clients');
        $this->assertSoftDeleted('clients', ['id' => $client->id]);
    }

    public function test_validation_requires_mandatory_fields(): void
    {
        $this->actingAs($this->admin())
            ->from('/clients/create')
            ->post('/clients', [])
            ->assertSessionHasErrors(['name', 'email', 'phone', 'document']);
    }

    public function test_email_must_be_unique_on_create(): void
    {
        Client::factory()->create(['email' => 'dup@example.com']);

        $this->actingAs($this->admin())
            ->from('/clients/create')
            ->post('/clients', $this->validData(['email' => 'dup@example.com']))
            ->assertRedirect('/clients/create')
            ->assertSessionHasErrors('email');

        $this->assertDatabaseCount('clients', 1);
    }

    public function test_document_must_be_unique_on_create(): void
    {
        Client::factory()->create(['document' => '55555555555']);

        $this->actingAs($this->admin())
            ->from('/clients/create')
            ->post('/clients', $this->validData(['document' => '55555555555']))
            ->assertRedirect('/clients/create')
            ->assertSessionHasErrors('document');

        $this->assertDatabaseCount('clients', 1);
    }

    public function test_unique_rules_ignore_itself_on_update(): void
    {
        $client = Client::factory()->create([
            'email' => 'self@example.com',
            'document' => '77777777777',
        ]);

        // Atualizar mantendo o próprio e-mail e documento deve passar.
        $this->actingAs($this->admin())
            ->put("/clients/{$client->id}", $this->validData([
                'email' => 'self@example.com',
                'document' => '77777777777',
            ]))
            ->assertRedirect('/clients')
            ->assertSessionHasNoErrors();
    }

    public function test_email_must_be_unique_against_other_clients_on_update(): void
    {
        Client::factory()->create(['email' => 'other@example.com']);
        $client = Client::factory()->create(['email' => 'mine@example.com']);

        $this->actingAs($this->admin())
            ->from("/clients/{$client->id}/edit")
            ->put("/clients/{$client->id}", $this->validData(['email' => 'other@example.com']))
            ->assertRedirect("/clients/{$client->id}/edit")
            ->assertSessionHasErrors('email');
    }

    public function test_document_must_be_unique_against_other_clients_on_update(): void
    {
        Client::factory()->create(['document' => '88888888888']);
        $client = Client::factory()->create(['document' => '99999999999']);

        $this->actingAs($this->admin())
            ->from("/clients/{$client->id}/edit")
            ->put("/clients/{$client->id}", $this->validData(['document' => '88888888888']))
            ->assertRedirect("/clients/{$client->id}/edit")
            ->assertSessionHasErrors('document');
    }

    public function test_list_can_be_searched_by_name_email_or_document(): void
    {
        Client::factory()->create(['name' => 'Joana Encontrada', 'email' => 'joana@example.com', 'document' => '10101010101']);
        Client::factory()->create(['name' => 'Pedro Oculto', 'email' => 'pedro@example.com', 'document' => '20202020202']);

        $admin = $this->admin();

        // Por nome
        $this->actingAs($admin)
            ->get('/clients?search=joana')
            ->assertOk()
            ->assertSee('Joana Encontrada')
            ->assertDontSee('Pedro Oculto');

        // Por e-mail
        $this->actingAs($admin)
            ->get('/clients?search=pedro@example.com')
            ->assertOk()
            ->assertSee('Pedro Oculto')
            ->assertDontSee('Joana Encontrada');

        // Por documento
        $this->actingAs($admin)
            ->get('/clients?search=10101010101')
            ->assertOk()
            ->assertSee('Joana Encontrada')
            ->assertDontSee('Pedro Oculto');
    }

    public function test_client_edit_shows_their_contracts(): void
    {
        $client = Client::factory()->create();
        $package = Package::factory()->create(['name' => 'Pacote Vinculado']);
        Contract::factory()->create([
            'client_id' => $client->id,
            'package_id' => $package->id,
            'title' => 'Contrato do Cliente',
        ]);

        $this->actingAs($this->admin())
            ->get("/clients/{$client->id}/edit")
            ->assertOk()
            ->assertSee('Contrato do Cliente')
            ->assertSee('Pacote Vinculado');
    }

    public function test_client_edit_shows_empty_state_without_contracts(): void
    {
        $client = Client::factory()->create();

        $this->actingAs($this->admin())
            ->get("/clients/{$client->id}/edit")
            ->assertOk()
            ->assertSee('Nenhum contrato para este cliente');
    }

    public function test_client_edit_loads_contracts_without_n_plus_one(): void
    {
        $admin = $this->admin();
        $client = Client::factory()->create();
        // 5 contratos, cada um com seu pacote.
        Contract::factory()->count(5)->create(['client_id' => $client->id]);

        DB::enableQueryLog();
        $response = $this->actingAs($admin)->get("/clients/{$client->id}/edit");
        $queries = DB::getQueryLog();
        DB::disableQueryLog();

        $response->assertOk();
        // client (route binding) + contratos + pacotes (eager) = constante; sem N+1.
        // Sem eager loading, cada um dos 5 contratos faria uma query extra do pacote.
        $this->assertLessThanOrEqual(6, count($queries));
    }

    public function test_list_is_paginated(): void
    {
        Client::factory()->count(15)->create();

        $response = $this->actingAs($this->admin())->get('/clients');

        $response->assertOk();
        $clients = $response->viewData('clients');
        $this->assertSame(10, $clients->count());
        $this->assertSame(15, $clients->total());
        $this->assertSame(2, $clients->lastPage());
    }
}
