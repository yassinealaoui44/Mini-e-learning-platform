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
                'Informatique',
                'Cybersécurité',
                'Data Science & AI',
                'Génie Financier',
                'Génie Logiciel',
                'Génie Civil',
            ]),
            'niveau' => $this->faker->randomElement([
                '1re année',
                '2e année',
                '3e année',
                '4e année',
                '5e année',
            ]),
        ];
    }
}
