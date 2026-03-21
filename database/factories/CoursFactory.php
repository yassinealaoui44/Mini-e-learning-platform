<?php

namespace Database\Factories;
use App\Models\Model;
use  App\Models\Cours;
use App\Models\Tuteur;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Model>
 */
class CoursFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
   public function definition(): array
{
    return [
        'titre' => fake()->sentence(3),
        'description' => fake()->paragraph(),
        'statut' => fake()->randomElement(['publié', 'brouillon']),
        'id_tuteur' => Tuteur::factory(), // Creates a tutor if one isn't provided
    ];
}
}
