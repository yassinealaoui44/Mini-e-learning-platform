<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $trackMap = [
            'Computer science' => 'Informatique',
            'Cybersecurity' => 'Cybersécurité',
            'Data science and AI' => 'Data Science & AI',
            'Financial engineering' => 'Génie Financier',
            'Software engineering' => 'Génie Logiciel',
            'Civil engineering' => 'Génie Civil',
            'Data science et IA' => 'Data Science & AI',
            'Ingénierie financière' => 'Génie Financier',
            'Génie logiciel' => 'Génie Logiciel',
            'Génie civil' => 'Génie Civil',
        ];

        foreach ($trackMap as $from => $to) {
            DB::table('cours')->where('filiere', $from)->update(['filiere' => $to]);
            DB::table('etudiants')->where('filiere', $from)->update(['filiere' => $to]);
            DB::table('tuteurs')->where('domaine', $from)->update(['domaine' => $to]);
        }

        $levelMap = [
            '1ere annee' => '1re année',
            '2eme annee' => '2e année',
            '3eme annee' => '3e année',
            '4eme annee' => '4e année',
            '5eme annee' => '5e année',
            '1st year' => '1re année',
            '2nd year' => '2e année',
            '3rd year' => '3e année',
            '4th year' => '4e année',
            '5th year' => '5e année',
        ];

        foreach ($levelMap as $from => $to) {
            DB::table('etudiants')->where('niveau', $from)->update(['niveau' => $to]);
        }
    }

    public function down(): void
    {
    }
};
