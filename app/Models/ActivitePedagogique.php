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
        'enseignant_id', 'cours_id', 'annee_academique_id', 'validateur_id',
        'calculation_inputs', 'calculation_hash',
    ];

    protected $casts = [
        'date_saisie'         => 'datetime',
        'date_validation'     => 'datetime',
        'volume_horaire'      => 'decimal:2',
        'calculation_inputs'  => 'array',
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

    public function anneeAcademique()
    {
        return $this->belongsTo(AnneeAcademique::class);
    }

    public function validateur()
    {
        return $this->belongsTo(User::class, 'validateur_id');
    }

    // Association "porter" : une activité peut porter sur plusieurs ressources
    public function ressources()
    {
        return $this->hasMany(RessourcePedagogique::class, 'activite_id');
    }

    protected static function booted(): void
    {
        // Calcul automatique du volume horaire à la création
        static::creating(function (ActivitePedagogique $activite) {
            $activite->appliquerCalcul();
        });

        // Correctif audit (Moyenne) : comportement d'édition avant
        // validation désormais explicite.
        // - Une activité verrouillée ne peut plus être modifiée.
        // - Si un champ source du calcul change (type_action,
        //   niveau_contenu, cours_id), le volume horaire, les inputs
        //   et la signature d'intégrité sont recalculés automatiquement,
        //   plutôt que de laisser volume_horaire divergent des champs
        //   affichés à l'enseignant.
        static::updating(function (ActivitePedagogique $activite) {
            if ($activite->getOriginal('statut') === 'verrouille') {
                throw new \RuntimeException(
                    'Cette activité est verrouillée et ne peut plus être modifiée.'
                );
            }

            if ($activite->isDirty(['type_action', 'niveau_contenu', 'cours_id'])) {
                $activite->appliquerCalcul();
            }
        });
    }

    /**
     * Calcule le volume horaire à partir du cours et de la grille de
     * coefficients courante, puis fige les paramètres utilisés dans
     * calculation_inputs et leur signature dans calculation_hash.
     *
     * Correctif audit (Critique) : auparavant, seul volume_horaire
     * était stocké — une modification directe en base était
     * indétectable. calculation_hash permet de vérifier a posteriori
     * qu'un volume horaire n'a pas été altéré (voir estIntegre()).
     */
    protected function appliquerCalcul(): void
    {
        $cours = Cours::find($this->cours_id);

        if (! $cours) {
            return;
        }

        $coefficient = ParametreCalcul::getCoefficient(
            $this->type_action,
            $this->niveau_contenu
        );

        $nombreSequences = $cours->nombre_sequences;

        $this->annee_academique_id = $cours->annee_academique_id;
        $this->volume_horaire = round($nombreSequences * $coefficient, 2);

        $inputs = [
            'cours_id'         => $cours->id,
            'nombre_sequences' => $nombreSequences,
            'type_action'      => $this->type_action,
            'niveau_contenu'   => $this->niveau_contenu,
            'coefficient'      => $coefficient,
            'volume_horaire'   => (float) $this->volume_horaire,
        ];

        $this->calculation_inputs = $inputs;
        $this->calculation_hash = static::signer($inputs);
    }

    /**
     * Signe un jeu de paramètres de calcul avec la clé applicative.
     */
    public static function signer(array $inputs): string
    {
        return hash_hmac(
            'sha256',
            json_encode($inputs, JSON_UNESCAPED_UNICODE),
            config('app.key')
        );
    }

    /**
     * Vérifie que volume_horaire n'a pas été altéré depuis son
     * dernier calcul, en recomparant la signature stockée à celle
     * recalculée à partir de calculation_inputs.
     */
    public function estIntegre(): bool
    {
        if (! $this->calculation_inputs || ! $this->calculation_hash) {
            return false;
        }

        return hash_equals(
            static::signer($this->calculation_inputs),
            $this->calculation_hash
        );
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
