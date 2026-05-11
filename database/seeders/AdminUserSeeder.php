<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Utilisateur;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $adminUser = Utilisateur::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@admin.com')],
            [
                'prenom' => 'Platform',
                'nom' => 'Admin',
                // Utilisateur casts password as "hashed" so we store plaintext here.
                'password' => env('ADMIN_PASSWORD', 'password123'),
            ],
        );

        Admin::updateOrCreate([
            'id_utilisateur' => $adminUser->id_utilisateur,
        ]);
    }
}
