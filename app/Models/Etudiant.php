<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Etudiant extends Model
{
    use HasFactory;

    protected $table = 'etudiants';

    protected $primaryKey = 'id_utilisateur';
    public $incrementing = false;

    protected $fillable = [
        'id_utilisateur',
        'filiere',
        'niveau',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(Utilisateur::class, 'id_utilisateur', 'id_utilisateur');
    }

    public function inscriptions(): HasMany
    {
        return $this->hasMany(Inscription::class, 'id_etudiant', 'id_utilisateur');
    }

    public function profile(): BelongsTo
    {
        return $this->belongsTo(Utilisateur::class, 'id_utilisateur', 'id_utilisateur');
    }

    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(
            Cours::class,
            'inscriptions',
            'id_etudiant',
            'id_cours',
            'id_utilisateur',
            'id',
        )->withPivot('date_inscription')->withTimestamps();
    }
}
