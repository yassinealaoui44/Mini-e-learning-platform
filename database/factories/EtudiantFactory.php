<?php

namespace Database\Factories;

use App\Models\Etudiant;
use Illuminate\Database\Eloquent\Factories\Factory;

class EtudiantFactory extends Factory
{
    protected $model = Etudiant::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_utilisateur' => \App\Models\Utilisateur::factory(),
            'filiere' => $this->faker->randomElement([
                'Computer science',
                'Cybersecurity',
                'Data science and AI',
                'Financial engineering',
                'Software engineering',
                'Civil engineering',
            ]),
            'niveau' => $this->faker->randomElement([
                '1ere annee',
                '2eme annee',
                '3eme annee',
                '4eme annee',
                '5eme annee',
            ]),
        ];
    }
}
