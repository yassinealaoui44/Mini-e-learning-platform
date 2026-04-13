<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lecon extends Model
{
    use HasFactory;

    protected $table = 'lecons';

    protected $fillable = [
        'titre',
        'type',
        'file_path',
        'cours_id',
    ];

    public function cours(): BelongsTo
    {
        return $this->belongsTo(Cours::class, 'cours_id');
    }

    public function commentaires(): HasMany
    {
        return $this->hasMany(Commentaire::class, 'id_lecon');
    }
}
