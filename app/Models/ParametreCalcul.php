<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ParametreCalcul extends Model
{
    use HasFactory;

    protected $table = 'parametres_calcul';

    protected $fillable = [
        'type_action', 'niveau_contenu', 'coefficient', 'sequences_par_credit'
    ];

    protected $casts = [
        'coefficient' => 'decimal:3',
    ];

    // Récupérer le coefficient pour un type et un niveau donnés
    public static function getCoefficient(string $typeAction, int $niveauContenu): float
    {
        $parametre = static::where('type_action', $typeAction)
                           ->where('niveau_contenu', $niveauContenu)
                           ->first();

        return $parametre ? (float) $parametre->coefficient : 0;
    }

    /**
     * Valeur courante de sequences_par_credit.
     *
     * Correctif audit (Haute) : ce point de lecture unique remplace
     * l'ancienne colonne générée cours.nombre_sequences (STORED × 40),
     * qui figeait la valeur 40 en dur et divergeait silencieusement
     * dès que l'administrateur modifiait ce paramètre.
     */
    public static function sequencesParCredit(): int
    {
        return (int) (static::query()->value('sequences_par_credit') ?? 40);
    }
}