namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tuteur extends Model
{
    protected $primaryKey = 'id_utilisateur';
    public $incrementing = false;
    protected $fillable = ['id_utilisateur'];

    public function user()
    {
        return $this->belongsTo(Utilisateur::class, 'id_utilisateur');
    }

    // A tutor creates many courses
    public function courses()
    {
        return $this->hasMany(Cours::class, 'id_tuteur', 'id_utilisateur');
    }
}