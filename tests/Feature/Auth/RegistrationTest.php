<?php

namespace Tests\Feature\Auth;

use App\Models\Etudiant;
use App\Models\Utilisateur;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'prenom' => 'Test',
            'nom' => 'User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'etudiant',
            'filiere' => 'Computer science',
            'niveau' => '1ere annee',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('etudiant.dashboard'));

        $user = Utilisateur::query()->where('email', 'test@example.com')->first();

        $this->assertNotNull($user);
        $this->assertTrue(Hash::check('password', $user->password));
        $this->assertDatabaseHas('etudiants', [
            'id_utilisateur' => $user->id_utilisateur,
        ]);
    }
}
