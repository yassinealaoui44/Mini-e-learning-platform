<?php

namespace Database\Factories;

use App\Models\Model;
use App\Models\Cours;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Model>
 */
class LeconFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
        'titre' => fake()->sentence(5),
        'contenu' => fake()->text(500),
        'video_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', // Classic test URL
        'id_cours' => Cours::factory(),
        ];
    }
}
