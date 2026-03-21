<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Inscription extends Model
{
    use HasFactory;

    // Matching your custom primary key naming
    protected $primaryKey = 'id_inscription';

    // Columns that can be mass-assigned
    protected $fillable = [
        'id_etudiant',
        'id_cours',
        'date_inscription'
    ];

    /**
     * Relationship: Get the student who owns this enrollment.
     */
    public function etudiant()
    {
        // We specify 'id_etudiant' as the foreign key
        // and 'id_utilisateur' as the owner key in the etudiants table
        return $this->belongsTo(Etudiant::class, 'id_etudiant', 'id_utilisateur');
    }

    /**
     * Relationship: Get the course associated with this enrollment.
     */
    public function course()
    {
        return $this->belongsTo(Cours::class, 'id_cours', 'id_cours');
    }
}