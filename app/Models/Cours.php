<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cours extends Model
{
    use HasFactory;
    protected $primaryKey = 'id_cours';
    protected $fillable = ['titre', 'description', 'statut', 'id_tuteur'];

    public function tuteur()
    {
        return $this->belongsTo(Tuteur::class, 'id_tuteur', 'id_utilisateur');
    }

    public function lessons()
    {
        return $this->hasMany(Lecon::class, 'id_cours', 'id_cours');
    }

    public function students()
    {
        return $this->hasMany(Inscription::class, 'id_cours', 'id_cours');
    }
}
?>