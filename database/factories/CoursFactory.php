<?php

namespace Database\Factories;

use App\Models\Cours;
use App\Models\Tuteur;
use Illuminate\Database\Eloquent\Factories\Factory;

class CoursFactory extends Factory
{
    protected $model = Cours::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nom' => fake()->sentence(3),
            'filiere' => fake()->randomElement([
                'Informatique',
                'Cybersécurité',
                'Data Science & AI',
                'Génie Financier',
                'Génie Logiciel',
                'Génie Civil',
            ]),
            'thumbnail' => null,
            'id_tuteur' => Tuteur::factory(),
        ];
    }
}
