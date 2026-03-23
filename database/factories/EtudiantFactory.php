<?php

namespace Database\Factories;

use App\Models\Model;
use  App\Models\Utilisateur;
use App\Models\Etudiant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Model>
 */
class EtudiantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
{
    return [
        // 🚀 This creates a Utilisateur and grabs its ID automatically
        'id_utilisateur' => \App\Models\Utilisateur::factory(), 
        'cne' => $this->faker->unique()->bothify('??#######'),
        'filiere' => 'IIR',
    ];
}
}
