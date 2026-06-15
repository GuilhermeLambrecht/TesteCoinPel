<?php

namespace Tests\Feature\Drivers;

use App\Models\Driver;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class DriverCrudTest extends TestCase
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
            'name' => 'João Silva',
            'cpf' => '12345678901',
            'cnh' => '98765432100',
            'cnh_category' => 'D',
            'cnh_expiration' => '2030-12-31',
            'phone' => '(53) 99999-0000',
            'active' => '1',
        ], $overrides);
    }

    public function test_guest_cannot_access_drivers(): void
    {
        $this->get('/drivers')->assertRedirect('/login');
        $this->post('/drivers', $this->validData())->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_drivers_list(): void
    {
        Driver::factory()->create(['name' => 'Carlos Roberto']);

        $this->actingAs($this->admin())
            ->get('/drivers')
            ->assertOk()
            ->assertSee('Carlos Roberto');
    }

    public function test_driver_can_be_created(): void
    {
        $response = $this->actingAs($this->admin())
            ->post('/drivers', $this->validData());

        $response->assertRedirect('/drivers');
        $response->assertSessionHas('status');
        $this->assertDatabaseHas('drivers', [
            'name' => 'João Silva',
            'cpf' => '12345678901',
            'cnh_category' => 'D',
        ]);
    }

    public function test_driver_can_be_created_without_a_photo(): void
    {
        // A foto é opcional: o cadastro funciona sem enviar 'photo'.
        $response = $this->actingAs($this->admin())
            ->post('/drivers', $this->validData());

        $response->assertRedirect('/drivers')->assertSessionHasNoErrors();
        $this->assertDatabaseHas('drivers', [
            'name' => 'João Silva',
            'photo_path' => null,
        ]);
    }

    public function test_driver_can_be_created_with_a_valid_photo(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin())
            ->post('/drivers', $this->validData([
                'photo' => UploadedFile::fake()->image('original-name.jpg'),
            ]))
            ->assertRedirect('/drivers')
            ->assertSessionHasNoErrors();

        $driver = Driver::firstWhere('name', 'João Silva');

        $this->assertNotNull($driver->photo_path);
        Storage::disk('public')->assertExists($driver->photo_path);

        // O nome é gerado pelo Laravel, nunca o nome original enviado.
        $this->assertStringNotContainsString('original-name', $driver->photo_path);
        $this->assertStringStartsWith('drivers/', $driver->photo_path);
    }

    public function test_photo_must_be_an_image(): void
    {
        Storage::fake('public');

        $this->actingAs($this->admin())
            ->from('/drivers/create')
            ->post('/drivers', $this->validData([
                'photo' => UploadedFile::fake()->create('document.pdf', 100, 'application/pdf'),
            ]))
            ->assertRedirect('/drivers/create')
            ->assertSessionHasErrors('photo');

        $this->assertDatabaseCount('drivers', 0);
    }

    public function test_photo_must_not_exceed_the_max_size(): void
    {
        Storage::fake('public');

        // 3MB excede o limite de 2MB (2048 KB).
        $this->actingAs($this->admin())
            ->from('/drivers/create')
            ->post('/drivers', $this->validData([
                'photo' => UploadedFile::fake()->image('huge.jpg')->size(3000),
            ]))
            ->assertRedirect('/drivers/create')
            ->assertSessionHasErrors('photo');

        $this->assertDatabaseCount('drivers', 0);
    }

    public function test_updating_photo_replaces_the_old_file(): void
    {
        Storage::fake('public');

        $oldPath = UploadedFile::fake()->image('old.jpg')->store('drivers', 'public');
        $driver = Driver::factory()->create(['photo_path' => $oldPath]);

        $this->actingAs($this->admin())
            ->put("/drivers/{$driver->id}", $this->validData([
                'cpf' => $driver->cpf,
                'photo' => UploadedFile::fake()->image('new.jpg'),
            ]))
            ->assertRedirect('/drivers')
            ->assertSessionHasNoErrors();

        $driver->refresh();

        $this->assertNotSame($oldPath, $driver->photo_path);
        Storage::disk('public')->assertExists($driver->photo_path);
        Storage::disk('public')->assertMissing($oldPath);
    }

    public function test_listing_shows_placeholder_when_driver_has_no_photo(): void
    {
        Driver::factory()->create(['name' => 'Motorista Sem Foto', 'photo_path' => null]);

        $this->actingAs($this->admin())
            ->get('/drivers')
            ->assertOk()
            ->assertSee('Motorista Sem Foto')
            // Sem foto → nenhum <img> apontando para o storage; usa o avatar padrão.
            ->assertDontSee('/storage/drivers/');
    }

    public function test_listing_shows_the_uploaded_photo(): void
    {
        Storage::fake('public');

        $path = UploadedFile::fake()->image('avatar.jpg')->store('drivers', 'public');
        Driver::factory()->create(['name' => 'Motorista Com Foto', 'photo_path' => $path]);

        $this->actingAs($this->admin())
            ->get('/drivers')
            ->assertOk()
            ->assertSee('Motorista Com Foto')
            ->assertSee($path);
    }

    public function test_driver_can_be_updated(): void
    {
        $driver = Driver::factory()->create(['cpf' => '11111111111', 'name' => 'Antigo']);

        $response = $this->actingAs($this->admin())
            ->put("/drivers/{$driver->id}", $this->validData([
                'cpf' => '22222222222',
                'name' => 'Nome Novo',
            ]));

        $response->assertRedirect('/drivers');
        $this->assertDatabaseHas('drivers', [
            'id' => $driver->id,
            'cpf' => '22222222222',
            'name' => 'Nome Novo',
        ]);
    }

    public function test_driver_can_be_soft_deleted(): void
    {
        $driver = Driver::factory()->create();

        $response = $this->actingAs($this->admin())
            ->delete("/drivers/{$driver->id}");

        $response->assertRedirect('/drivers');
        $this->assertSoftDeleted('drivers', ['id' => $driver->id]);
    }

    public function test_cpf_must_be_unique_on_create(): void
    {
        Driver::factory()->create(['cpf' => '55555555555']);

        $response = $this->actingAs($this->admin())
            ->from('/drivers/create')
            ->post('/drivers', $this->validData(['cpf' => '55555555555']));

        $response->assertRedirect('/drivers/create');
        $response->assertSessionHasErrors('cpf');
        $this->assertDatabaseCount('drivers', 1);
    }

    public function test_cpf_unique_rule_ignores_itself_on_update(): void
    {
        $driver = Driver::factory()->create(['cpf' => '77777777777']);

        $this->actingAs($this->admin())
            ->put("/drivers/{$driver->id}", $this->validData(['cpf' => '77777777777']))
            ->assertRedirect('/drivers')
            ->assertSessionHasNoErrors();
    }

    public function test_cpf_must_be_unique_against_other_drivers_on_update(): void
    {
        $other = Driver::factory()->create(['cpf' => '88888888888']);
        $driver = Driver::factory()->create(['cpf' => '99999999999']);

        $this->actingAs($this->admin())
            ->from("/drivers/{$driver->id}/edit")
            ->put("/drivers/{$driver->id}", $this->validData(['cpf' => '88888888888']))
            ->assertRedirect("/drivers/{$driver->id}/edit")
            ->assertSessionHasErrors('cpf');
    }

    public function test_validation_requires_mandatory_fields(): void
    {
        $this->actingAs($this->admin())
            ->from('/drivers/create')
            ->post('/drivers', [])
            ->assertSessionHasErrors(['name', 'cpf', 'cnh', 'cnh_category', 'cnh_expiration', 'phone']);
    }

    public function test_list_can_be_searched_by_name_or_cpf(): void
    {
        Driver::factory()->create(['name' => 'Maria Aparecida', 'cpf' => '10101010101']);
        Driver::factory()->create(['name' => 'Pedro Souza', 'cpf' => '20202020202']);

        $admin = $this->admin();

        // Busca por nome
        $this->actingAs($admin)
            ->get('/drivers?search=maria')
            ->assertOk()
            ->assertSee('Maria Aparecida')
            ->assertDontSee('Pedro Souza');

        // Busca por CPF
        $this->actingAs($admin)
            ->get('/drivers?search=20202020202')
            ->assertOk()
            ->assertSee('Pedro Souza')
            ->assertDontSee('Maria Aparecida');
    }

    public function test_list_is_paginated(): void
    {
        Driver::factory()->count(15)->create();

        $response = $this->actingAs($this->admin())->get('/drivers');

        $response->assertOk();
        $drivers = $response->viewData('drivers');
        $this->assertSame(10, $drivers->count());
        $this->assertSame(15, $drivers->total());
        $this->assertSame(2, $drivers->lastPage());
    }
}
