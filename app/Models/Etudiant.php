<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Etudiant extends Model
{
    use HasFactory;
    protected $primaryKey = 'id_utilisateur';
    public $incrementing = false; 
    protected $fillable = ['id_utilisateur'];

    // Link back to the main account
    public function user()
    {
        return $this->belongsTo(Utilisateur::class, 'id_utilisateur');
    }

    // A student can have many enrollments
    public function inscriptions()
    {
        return $this->hasMany(Inscription::class, 'id_etudiant', 'id_utilisateur');
    }
    public function profile()
    {
        return $this->belongsTo(Utilisateur::class, 'id_utilisateur');
    }
}
?>