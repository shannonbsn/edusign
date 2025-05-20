<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cours extends Model
{
    protected $table = 'cours';

    protected $fillable = [
        'nom',
        'classe_id',
        'intervenant_id',
        'salle',
        'date',
        'h_debut',
        'h_fin',
        'duree'
    ];

    public function classe()
    {
        return $this->belongsTo(Classe::class);
    }

    public function intervenant()
    {
        return $this->belongsTo(User::class, 'intervenant_id');
    }

    public function salleAssociee()
    {
        return $this->belongsTo(Salle::class, 'salle');
    }

    public function presences()
    {
        return $this->hasMany(Presence::class);
    }
}
