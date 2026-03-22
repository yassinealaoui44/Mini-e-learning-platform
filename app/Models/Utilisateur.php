<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Utilisateur extends Authenticatable
{
    use HasFactory, Notifiable;

    // 1. Tell Laravel the exact table name in the database
    protected $table = 'utilisateurs';
    protected $primaryKey = 'id_utilisateur';

    // 2. Define which fields can be filled during registration
    protected $fillable = [
        'nom',
        'email',
        'password',
    ];

    // 3. Hide the password when the user data is converted to JSON (for security)
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // 4. Tell Laravel to use 'mot_de_passe' instead of the default 'password' column
    public function getAuthPassword()
    {
        return $this->mot_de_passe;
    }

    /**
     * RELATIONSHIPS (The "Class Table Inheritance" Links)
     */

    // Link to the Etudiant profile
    public function etudiant()
    {
        return $this->hasOne(Etudiant::class, 'id_utilisateur');
    }

    // Link to the Tuteur profile
    public function tuteur()
    {
        return $this->hasOne(Tuteur::class, 'id_utilisateur');
    }

    // Link to the Admin profile
    public function admin()
    {
        return $this->hasOne(admin::class, 'id_utilisateur');
    }
}