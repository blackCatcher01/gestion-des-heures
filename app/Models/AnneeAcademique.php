<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AnneeAcademique extends Model
{
    use HasFactory;

    protected $table = 'annees_academiques';

    protected $fillable = [
        'libelle', 'date_debut', 'date_fin', 'est_active'
    ];

    protected $casts = [
        'date_debut' => 'date',
        'date_fin'   => 'date',
        'est_active' => 'boolean',
    ];

    // Une année a plusieurs cours
    public function cours()
    {
        return $this->hasMany(Cours::class);
    }

    // Récupérer l'année active (méthode statique utilitaire)
    public static function active()
    {
        return static::where('est_active', true)->first();
    }
}