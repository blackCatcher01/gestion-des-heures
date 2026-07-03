<?php

namespace App\Http\Controllers;

use App\Models\Enseignant;
use App\Models\AnneeAcademique;
use App\Models\ActivitePedagogique;
use App\Models\ParametreCalcul;
use Illuminate\Http\Request;

class CalculHeuresController extends Controller
{
    public function index(Request $request)
    {
        $anneeActive = AnneeAcademique::active();

        $enseignants = Enseignant::with(['departement', 'user'])
            ->when($request->departement, fn($q) =>
                $q->where('departement_id', $request->departement)
            )
            ->when($request->statut, fn($q) =>
                $q->where('statut', $request->statut)
            )
            ->withSum(['activites as heures_validees' => function ($q) use ($anneeActive) {
                $q->where('statut', 'valide')
                  ->when($anneeActive, fn($q2) =>
                      $q2->whereHas('cours', fn($q3) =>
                          $q3->where('annee_academique_id', $anneeActive->id)
                      )
                  );
            }], 'volume_horaire')
            ->orderByDesc('heures_validees')
            ->get();

        $parametres   = ParametreCalcul::all()->keyBy(fn($p) => $p->type_action.'_'.$p->niveau_contenu);
        $departements = \App\Models\Departement::orderBy('nom')->get();

        return view('calcul-heures.index', compact(
            'enseignants', 'anneeActive', 'parametres', 'departements'
        ));
    }

    public function detail(Enseignant $enseignant)
    {
        $anneeActive = AnneeAcademique::active();

        $activites = $enseignant->activites()
            ->with('cours')
            ->where('statut', 'valide')
            ->when($anneeActive, fn($q) =>
                $q->whereHas('cours', fn($q2) =>
                    $q2->where('annee_academique_id', $anneeActive->id)
                )
            )
            ->get();

        $volumeTotal = $activites->sum('volume_horaire');
        $montantTotal = $volumeTotal * $enseignant->taux_horaire;

        return view('calcul-heures.detail', compact(
            'enseignant', 'activites', 'volumeTotal', 'montantTotal', 'anneeActive'
        ));
    }
}