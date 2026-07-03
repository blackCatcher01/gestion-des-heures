<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Enseignant extends Model
{
    use HasFactory;

    protected $table = 'enseignants';

    protected $fillable = [
        'nom', 'prenom', 'grade', 'statut',
        'taux_horaire', 'telephone',
        'user_id', 'departement_id',
    ];

    // Relation vers le compte utilisateur
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relation vers le département
    public function departement()
    {
        return $this->belongsTo(Departement::class);
    }

    // Un enseignant a plusieurs activités pédagogiques
    public function activites()
    {
        return $this->hasMany(ActivitePedagogique::class);
    }

    // Nom complet formaté
    public function getNomCompletAttribute(): string
    {
        return $this->prenom . ' ' . $this->nom;
    }

    // Statut de validation représentatif (pour affichage synthétique en liste)
    public function statutValidation(): ?string
    {
        if ($this->activites()->where('statut', 'en_attente')->exists()) {
            return 'en_attente';
        }
        if ($this->activites()->where('statut', 'rejete')->exists()) {
            return 'rejete';
        }
        if ($this->activites()->where('statut', 'valide')->exists()) {
            return 'valide';
        }
        return null;
    }

    // Volume horaire total validé pour une année
    public function volumeHoraireValide(?int $anneeId = null): float
    {
        $query = $this->activites()->where('statut', 'valide');

        if ($anneeId) {
            $query->whereHas('cours', function ($q) use ($anneeId) {
                $q->where('annee_academique_id', $anneeId);
            });
        }

        return (float) $query->sum('volume_horaire');
    }
}