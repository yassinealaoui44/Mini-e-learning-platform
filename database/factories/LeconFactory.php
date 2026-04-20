<?php

namespace Database\Factories;

use App\Models\Cours;
use App\Models\Lecon;
use Illuminate\Database\Eloquent\Factories\Factory;

class LeconFactory extends Factory
{
    protected $model = Lecon::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'titre' => fake()->sentence(5),
            'type' => fake()->randomElement(['pdf', 'video']),
            'file_path' => 'lecons/demo-resource.pdf',
            'cours_id' => Cours::factory(),
        ];
    }
}
