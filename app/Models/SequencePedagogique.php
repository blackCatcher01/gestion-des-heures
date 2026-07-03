<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SequencePedagogique extends Model
{
    use HasFactory;

    protected $table = 'sequences_pedagogiques';

    protected $fillable = ['titre', 'numero_ordre', 'description', 'cours_id'];

    // Une séquence appartient à un cours
    public function cours()
    {
        return $this->belongsTo(Cours::class);
    }

    // Une séquence contient plusieurs ressources
    public function ressources()
    {
        return $this->hasMany(RessourcePedagogique::class, 'sequence_id');
    }
}