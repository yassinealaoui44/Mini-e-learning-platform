<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
  public function run(): void
{
    // 1. Create 10 Students (This also creates 10 Utilisateurs automatically)
    \App\Models\Etudiant::factory(10)->create();

    // 2. Create 3 Tutors (This also creates 3 Utilisateurs)
    $tuteurs = \App\Models\Tuteur::factory(3)->create();

    // 3. Create 5 Courses and assign them to those Tutors
    foreach ($tuteurs as $tuteur) {
        $courses = \App\Models\Cours::factory(2)->create([
            'id_tuteur' => $tuteur->id_utilisateur
        ]);

        // 4. For each Course, create 3 Lessons
        foreach ($courses as $course) {
            \App\Models\Lecon::factory(3)->create([
                'id_cours' => $course->id_cours
            ]);
        }
    }
    
    // 5. Enroll random students into random courses
    $allStudents = \App\Models\Etudiant::all();
    $allCourses = \App\Models\Cours::all();

    foreach ($allStudents as $student) {
        \App\Models\Inscription::factory()->create([
            'id_etudiant' => $student->id_utilisateur,
            'id_cours' => $allCourses->random()->id_cours
        ]);
    }
}
}
