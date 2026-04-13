<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inscription extends Model
{
    use HasFactory;

    protected $table = 'inscriptions';

    protected $primaryKey = 'id_inscription';

    protected $fillable = [
        'id_etudiant',
        'id_cours',
        'date_inscription',
    ];

    public function etudiant(): BelongsTo
    {
        return $this->belongsTo(Etudiant::class, 'id_etudiant', 'id_utilisateur');
    }

    public function cours(): BelongsTo
    {
        return $this->belongsTo(Cours::class, 'id_cours', 'id');
    }

    public function course(): BelongsTo
    {
        return $this->cours();
    }
}
