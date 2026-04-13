<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Utilisateur extends Authenticatable
{
    use Notifiable;

    protected $table = 'utilisateurs';
    
    // 🚀 THE FIX: You MUST define this or $user->id_utilisateur will be NULL
    protected $primaryKey = 'id_utilisateur';

    protected $fillable = [
        'prenom',
        'nom',
        'email',
        'password',
    ];

    // Disable password hashing if you are storing plain text
    protected function casts(): array
    {
        return [
            // 'password' => 'hashed', <--- MAKE SURE THIS IS COMMENTED OUT
        ];
    }
}