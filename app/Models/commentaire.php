<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Commentaire extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_commentaire';

    protected $fillable = [
        'contenu',
        'id_lecon',
        'id_etudiant',
        'date_commentaire'
    ];

    /**
     * Relationship: Get the student who wrote the comment.
     */
    public function etudiant()
    {
        return $this->belongsTo(Etudiant::class, 'id_etudiant', 'id_utilisateur');
    }

    /**
     * Relationship: Get the lesson this comment belongs to.
     */
    public function lesson()
    {
        return $this->belongsTo(Lecon::class, 'id_lecon', 'id_lecon');
    }
}