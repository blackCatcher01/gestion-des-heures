<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ActivitePedagogique extends Model
{
    use HasFactory;

    protected $table = 'activites_pedagogiques';

    protected $fillable = [
        'type_action', 'niveau_contenu', 'volume_horaire', 'statut',
        'date_saisie', 'date_validation', 'commentaire_rejet',
        'enseignant_id', 'cours_id', 'validateur_id',
    ];

    protected $casts = [
        'date_saisie'     => 'datetime',
        'date_validation' => 'datetime',
        'volume_horaire'  => 'decimal:2',
    ];

    // Relations
    public function enseignant()
    {
        return $this->belongsTo(Enseignant::class);
    }

    public function cours()
    {
        return $this->belongsTo(Cours::class);
    }

    public function validateur()
    {
        return $this->belongsTo(User::class, 'validateur_id');
    }

    // Calcul automatique du volume horaire avant création
    protected static function booted(): void
    {
        static::creating(function (ActivitePedagogique $activite) {
            $cours = Cours::find($activite->cours_id);

            if ($cours) {
                $coefficient = ParametreCalcul::getCoefficient(
                    $activite->type_action,
                    $activite->niveau_contenu
                );
                $activite->volume_horaire = $cours->nombre_sequences * $coefficient;
            }
        });
    }

    // Vérifier si la déclaration peut encore être modifiée
    public function estModifiable(): bool
    {
        return in_array($this->statut, ['en_attente', 'rejete']);
    }

    // Vérifier si la déclaration est verrouillée
    public function estVerrouillee(): bool
    {
        return $this->statut === 'verrouille';
    }
}