<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Cours extends Model
{
    use HasFactory;

    protected $table = 'cours';

    protected $appends = [
        'cover_image_url',
    ];

    protected $fillable = [
        'nom',
        'filiere',
        'thumbnail',
        'id_tuteur',
    ];

    public function getCoverImageUrlAttribute(): ?string
    {
        if (! $this->thumbnail) {
            return null;
        }

        return Storage::disk('public')->url($this->thumbnail);
    }

    public function tuteur(): BelongsTo
    {
        return $this->belongsTo(Tuteur::class, 'id_tuteur', 'id_utilisateur');
    }

    public function lecons(): HasMany
    {
        return $this->hasMany(Lecon::class, 'cours_id');
    }

    public function inscriptions(): HasMany
    {
        return $this->hasMany(Inscription::class, 'id_cours');
    }

    public function etudiants(): BelongsToMany
    {
        return $this->belongsToMany(
            Etudiant::class,
            'inscriptions',
            'id_cours',
            'id_etudiant',
            'id',
            'id_utilisateur',
        )->withPivot('date_inscription')->withTimestamps();
    }
}
