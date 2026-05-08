<?php

namespace Database\Seeders;

use App\Models\Admin;
use App\Models\Utilisateur;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $adminUser = Utilisateur::updateOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@admin.com')],
            [
                'prenom' => 'Platform',
                'nom' => 'Admin',
                'password' => Hash::make(env('ADMIN_PASSWORD', 'password123')),
            ],
        );

        Admin::updateOrCreate([
            'id_utilisateur' => $adminUser->id_utilisateur,
        ]);
    }
}
