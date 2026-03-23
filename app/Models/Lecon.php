<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Lecon extends Model
{
    use HasFactory;
    protected $primaryKey = 'id_lecon';
    protected $fillable = ['titre', 'contenu', 'video_url', 'id_cours'];

    public function course()
    {
        return $this->belongsTo(Cours::class, 'id_cours', 'id_cours');
    }
}
?>