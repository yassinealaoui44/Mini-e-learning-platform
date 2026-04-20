<?php

namespace App\Models;

use Illuminate\Auth\Passwords\CanResetPassword as CanResetPasswordTrait;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Utilisateur extends Authenticatable implements CanResetPassword
{
    use HasFactory;
    use CanResetPasswordTrait;
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

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function adminProfile(): HasOne
    {
        return $this->hasOne(Admin::class, 'id_utilisateur', 'id_utilisateur');
    }

    public function tuteurProfile(): HasOne
    {
        return $this->hasOne(Tuteur::class, 'id_utilisateur', 'id_utilisateur');
    }

    public function etudiantProfile(): HasOne
    {
        return $this->hasOne(Etudiant::class, 'id_utilisateur', 'id_utilisateur');
    }
}
