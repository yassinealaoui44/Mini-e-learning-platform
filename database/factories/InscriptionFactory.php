<?php

namespace Database\Factories;

use App\Models\Inscription;
use App\Models\Cours;
use App\Models\Etudiant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Inscription>
 */
class InscriptionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
        'id_etudiant' => Etudiant::factory(),
        'id_cours' => Cours::factory(),
        'date_inscription' => now(),
        ];
    }
}
