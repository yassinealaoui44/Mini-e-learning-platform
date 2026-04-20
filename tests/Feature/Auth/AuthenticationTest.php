<?php

namespace Tests\Feature\Auth;

use App\Models\Admin;
use App\Models\Utilisateur;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = Utilisateur::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = Utilisateur::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = Utilisateur::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect(route('login'));
    }

    public function test_legacy_plaintext_passwords_are_upgraded_on_login(): void
    {
        $user = Utilisateur::query()->create([
            'prenom' => 'Legacy',
            'nom' => 'User',
            'email' => 'legacy@example.com',
            'password' => 'legacy-password',
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'legacy-password',
        ]);

        $this->assertAuthenticated();
        $this->assertNotSame('legacy-password', $user->fresh()->password);
    }

    public function test_authenticated_pages_send_no_store_headers(): void
    {
        $user = Utilisateur::factory()->create();

        $response = $this->actingAs($user)->get('/profile');

        $cacheControl = $response->headers->get('Cache-Control', '');

        $this->assertStringContainsString('no-store', $cacheControl);
        $this->assertStringContainsString('no-cache', $cacheControl);
        $this->assertStringContainsString('max-age=0', $cacheControl);
    }

    public function test_admin_users_are_redirected_to_the_admin_dashboard(): void
    {
        $user = Utilisateur::factory()->create();

        Admin::query()->create([
            'id_utilisateur' => $user->id_utilisateur,
        ]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('dashboard', absolute: false));
        $this->actingAs($user)
            ->get('/dashboard')
            ->assertRedirect(route('admin.dashboard'));
    }
}
