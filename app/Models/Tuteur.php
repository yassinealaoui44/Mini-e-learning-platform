<?
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
class Tuteur extends Model
{
    use HasFactory;
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
    public function profile()
    {
        return $this->belongsTo(Utilisateur::class, 'id_utilisateur');
    }
}
?>