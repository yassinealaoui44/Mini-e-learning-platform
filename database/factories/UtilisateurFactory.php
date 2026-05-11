<?php

namespace Database\Factories;

use App\Models\Utilisateur;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Utilisateur>
 */
class UtilisateurFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'prenom' => fake()->firstName(),
            'nom' => fake()->lastName(),
            'email' => fake()->unique->safeEmail(),
            // Utilisateur casts password as "hashed", so store plaintext here.
            'password' => 'password',
        ];
    }
}
