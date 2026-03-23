<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Admin extends Model
{
    use HasFactory;
    // 1. Tell Laravel the primary key isn't the default 'id'
    protected $primaryKey = 'id_utilisateur';

    // 2. Since the ID comes from the Utilisateur table, it doesn't auto-increment here
    public $incrementing = false;

    // 3. Allow mass assignment for the foreign key
    protected $fillable = ['id_utilisateur'];

    /**
     * Relationship: An Admin belongs to a general Utilisateur profile.
     * This allows you to do: $admin->profile->nom
     */
    public function profile(): BelongsTo
    {
        return $this->belongsTo(Utilisateur::class, 'id_utilisateur');
    }
}