<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordChangeTest extends TestCase
{
    use RefreshDatabase;

    public function test_first_access_user_is_redirected_to_password_change(): void
    {
        $user = User::factory()->create(['must_change_password' => true]);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertRedirect(route('password.change'));
    }

    public function test_first_access_user_can_view_change_password_screen(): void
    {
        $user = User::factory()->create(['must_change_password' => true]);

        $this->actingAs($user)
            ->get('/password/change')
            ->assertOk();
    }

    public function test_password_is_changed_on_first_access(): void
    {
        $user = User::factory()->create(['must_change_password' => true]);

        $response = $this->actingAs($user)->post('/password/change', [
            'password' => 'new-secret-password',
            'password_confirmation' => 'new-secret-password',
        ]);

        $response->assertRedirect(route('dashboard'));

        $user->refresh();
        $this->assertFalse($user->must_change_password);
        $this->assertTrue(Hash::check('new-secret-password', $user->password));
    }

    public function test_password_change_requires_minimum_length_and_confirmation(): void
    {
        $user = User::factory()->create(['must_change_password' => true]);

        $this->actingAs($user)
            ->from('/password/change')
            ->post('/password/change', [
                'password' => 'short',
                'password_confirmation' => 'mismatch',
            ])
            ->assertRedirect('/password/change')
            ->assertSessionHasErrors('password');

        $this->assertTrue($user->fresh()->must_change_password);
    }
}
