<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Cours extends Model
{
    use HasFactory;

    protected $table = 'cours';

    protected $fillable = [
        'intitule', 'filiere', 'niveau', 'semestre',
        'nombre_credits', 'annee_academique_id',
    ];

    protected $appends = ['nombre_sequences'];

    public function getNombreSequencesAttribute(): int
    {
        return (int) $this->nombre_credits * ParametreCalcul::sequencesParCredit();
    }

    // Relation vers l'année académique
    public function anneeAcademique()
    {
        return $this->belongsTo(AnneeAcademique::class);
    }

    // Un cours a plusieurs séquences
    public function sequences()
    {
        return $this->hasMany(SequencePedagogique::class)->orderBy('numero_ordre');
    }

    // Un cours a plusieurs activités pédagogiques déclarées dessus
    public function activites()
    {
        return $this->hasMany(ActivitePedagogique::class);
    }
}