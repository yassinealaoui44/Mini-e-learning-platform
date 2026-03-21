<?php

namespace Database\Factories;

use App\Models\Commentaire;
use App\Models\Lecon;
use App\Models\Etudiant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Commentaire>
 */
class CommentaireFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
   public function definition(): array
{
    return [
        'contenu' => fake()->sentence(),
        'id_lecon' => Lecon::factory(),
        'id_etudiant' => Etudiant::factory(),
    ];
}
}
