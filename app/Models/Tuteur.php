<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tuteur extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tuteurs';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id_utilisateur';

    /**
     * Indicates if the IDs are auto-incrementing.
     * * Set to false because this ID is shared from the 'utilisateurs' table.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_utilisateur',
        'domaine',
    ];

    /**
     * Get the user profile associated with the tuteur.
     * This links back to the parent 'utilisateurs' table.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(Utilisateur::class, 'id_utilisateur', 'id_utilisateur');
    }

    /**
     * Get the courses created by this tuteur.
     * * Linked via 'id_tuteur' in the 'cours' table.
     */
    public function courses(): HasMany
    {
        return $this->hasMany(Cours::class, 'id_tuteur', 'id_utilisateur');
    }
}