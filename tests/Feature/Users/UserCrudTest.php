<?php

namespace Tests\Feature\Users;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserCrudTest extends TestCase
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
            'name' => 'Maria Admin',
            'email' => 'maria@example.com',
            'password' => 'secret-password',
            'password_confirmation' => 'secret-password',
        ], $overrides);
    }

    public function test_guest_cannot_access_users(): void
    {
        $this->get('/users')->assertRedirect('/login');
        $this->post('/users', $this->validData())->assertRedirect('/login');
    }

    public function test_authenticated_user_can_view_list_without_exposing_password(): void
    {
        $user = User::factory()->create(['name' => 'Carlos Admin']);

        $this->actingAs($this->admin())
            ->get('/users')
            ->assertOk()
            ->assertSee('Carlos Admin')
            ->assertDontSee($user->password); // hash nunca aparece na listagem
    }

    public function test_user_is_created_with_hashed_password_and_must_change_password(): void
    {
        $response = $this->actingAs($this->admin())
            ->post('/users', $this->validData());

        $response->assertRedirect('/users');
        $response->assertSessionHas('status');

        $created = User::where('email', 'maria@example.com')->first();
        $this->assertNotNull($created);
        $this->assertTrue($created->must_change_password);
        $this->assertTrue(Hash::check('secret-password', $created->password));
        $this->assertNotSame('secret-password', $created->password);
    }

    public function test_email_must_be_unique_on_create(): void
    {
        User::factory()->create(['email' => 'taken@example.com']);

        $this->actingAs($this->admin())
            ->from('/users/create')
            ->post('/users', $this->validData(['email' => 'taken@example.com']))
            ->assertRedirect('/users/create')
            ->assertSessionHasErrors('email');
    }

    public function test_email_unique_rule_ignores_itself_on_update(): void
    {
        $user = User::factory()->create(['email' => 'self@example.com']);

        $this->actingAs($this->admin())
            ->put("/users/{$user->id}", [
                'name' => 'Self Updated',
                'email' => 'self@example.com',
            ])
            ->assertRedirect('/users')
            ->assertSessionHasNoErrors();
    }

    public function test_email_must_be_unique_against_other_users_on_update(): void
    {
        User::factory()->create(['email' => 'other@example.com']);
        $user = User::factory()->create(['email' => 'mine@example.com']);

        $this->actingAs($this->admin())
            ->from("/users/{$user->id}/edit")
            ->put("/users/{$user->id}", [
                'name' => 'Mine',
                'email' => 'other@example.com',
            ])
            ->assertRedirect("/users/{$user->id}/edit")
            ->assertSessionHasErrors('email');
    }

    public function test_password_is_required_and_validated_on_create(): void
    {
        // Ausente
        $this->actingAs($this->admin())
            ->from('/users/create')
            ->post('/users', $this->validData(['password' => '', 'password_confirmation' => '']))
            ->assertSessionHasErrors('password');

        // Muito curta / sem confirmação
        $this->actingAs($this->admin())
            ->from('/users/create')
            ->post('/users', $this->validData(['password' => 'short', 'password_confirmation' => 'different']))
            ->assertSessionHasErrors('password');
    }

    public function test_password_is_kept_when_left_empty_on_update(): void
    {
        // Factory cria a senha como hash de 'password'.
        $user = User::factory()->create(['name' => 'Antigo']);

        $this->actingAs($this->admin())
            ->put("/users/{$user->id}", [
                'name' => 'Nome Novo',
                'email' => $user->email,
                'password' => '',
                'password_confirmation' => '',
            ])
            ->assertRedirect('/users')
            ->assertSessionHasNoErrors();

        $user->refresh();
        $this->assertSame('Nome Novo', $user->name);
        $this->assertTrue(Hash::check('password', $user->password)); // senha mantida
    }

    public function test_password_is_changed_when_provided_on_update(): void
    {
        $user = User::factory()->create();

        $this->actingAs($this->admin())
            ->put("/users/{$user->id}", [
                'name' => $user->name,
                'email' => $user->email,
                'password' => 'brand-new-password',
                'password_confirmation' => 'brand-new-password',
            ])
            ->assertRedirect('/users');

        $this->assertTrue(Hash::check('brand-new-password', $user->fresh()->password));
    }

    public function test_user_cannot_delete_their_own_account(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->delete("/users/{$admin->id}")
            ->assertRedirect('/users')
            ->assertSessionHas('error');

        $this->assertNotSoftDeleted('users', ['id' => $admin->id]);
    }

    public function test_user_can_delete_another_user(): void
    {
        $other = User::factory()->create();

        $this->actingAs($this->admin())
            ->delete("/users/{$other->id}")
            ->assertRedirect('/users')
            ->assertSessionHas('status');

        $this->assertSoftDeleted('users', ['id' => $other->id]);
    }

    public function test_list_can_be_searched_by_name_or_email(): void
    {
        User::factory()->create(['name' => 'Fernanda Gestora', 'email' => 'fernanda@example.com']);
        User::factory()->create(['name' => 'Roberto Gestor', 'email' => 'roberto@example.com']);

        $admin = $this->admin();

        $this->actingAs($admin)
            ->get('/users?search=fernanda')
            ->assertOk()
            ->assertSee('Fernanda Gestora')
            ->assertDontSee('Roberto Gestor');

        $this->actingAs($admin)
            ->get('/users?search=roberto@example.com')
            ->assertOk()
            ->assertSee('Roberto Gestor')
            ->assertDontSee('Fernanda Gestora');
    }

    public function test_list_is_paginated(): void
    {
        // 1 admin autenticado + 14 = 15 usuários no total.
        User::factory()->count(14)->create();

        $response = $this->actingAs($this->admin())->get('/users');

        $response->assertOk();
        $users = $response->viewData('users');
        $this->assertSame(10, $users->count());
        $this->assertSame(15, $users->total());
        $this->assertSame(2, $users->lastPage());
    }
}
