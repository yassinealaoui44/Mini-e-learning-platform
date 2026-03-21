namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Etudiant extends Model
{
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
}