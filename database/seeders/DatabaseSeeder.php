<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Cours;
use App\Models\Etudiant;
use App\Models\Inscription;
use App\Models\Lecon;
use App\Models\Tuteur;
use App\Models\Utilisateur;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $adminUser = Utilisateur::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@learnlaravel.test')],
            [
                'prenom' => 'Super',
                'nom' => 'Admin',
                'password' => Hash::make(env('ADMIN_PASSWORD', 'admin12345')),
            ],
        );

        Admin::updateOrCreate([
            'id_utilisateur' => $adminUser->id_utilisateur,
        ]);

        $etudiants = Etudiant::factory(18)->create();
        $tuteurs = Tuteur::factory(6)->create();

        $courses = collect();

        foreach ($tuteurs as $tuteur) {
            $tuteurCourses = Cours::factory(fake()->numberBetween(2, 4))->create([
                'id_tuteur' => $tuteur->id_utilisateur,
            ]);

            foreach ($tuteurCourses as $course) {
                Lecon::factory(fake()->numberBetween(2, 5))->create([
                    'cours_id' => $course->id,
                ]);
            }

            $courses = $courses->merge($tuteurCourses);
        }

        foreach ($etudiants as $etudiant) {
            $selectedCourses = $courses->random(fake()->numberBetween(1, min(3, $courses->count())));

            foreach ($selectedCourses as $course) {
                Inscription::firstOrCreate(
                    [
                        'id_etudiant' => $etudiant->id_utilisateur,
                        'id_cours' => $course->id,
                    ],
                    [
                        'date_inscription' => now()->subDays(fake()->numberBetween(0, 20)),
                    ],
                );
            }
        }
    }
}
