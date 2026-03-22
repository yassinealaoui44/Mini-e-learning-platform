<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lecon extends Model
{
    protected $primaryKey = 'id_lecon';
    protected $fillable = ['titre', 'contenu', 'video_url', 'id_cours'];

    public function course()
    {
        return $this->belongsTo(Cours::class, 'id_cours', 'id_cours');
    }
}
?>