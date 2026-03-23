<?php

namespace Database\Factories;

use App\Models\Tuteur;
use App\Models\Utilisateur;
use Illuminate\Database\Eloquent\Factories\Factory;

class TuteurFactory extends Factory
{
    protected $model = Tuteur::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        return [
            // 🚀 This magic line creates a new Utilisateur and grabs its ID
            'id_utilisateur' => Utilisateur::factory(),

            // Role-specific "fake" data
            'matricule' => $this->faker->unique()->bothify('PROF-####-??'),
            'specialite' => $this->faker->randomElement([
                'Développement Web', 
                'Intelligence Artificielle', 
                'Cybersécurité', 
                'Réseaux & Systèmes', 
                'Cloud Computing'
            ]),
            'biographie' => $this->faker->paragraph(),
            
            // Standard timestamps
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}