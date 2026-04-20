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
            'id_utilisateur' => Utilisateur::factory(),
            'domaine' => $this->faker->randomElement([
                'Computer science',
                'Cybersecurity',
                'Data science and AI',
                'Financial engineering',
                'Software engineering',
                'Civil engineering',
            ]),
        ];
    }
}
