<?php

namespace App\Http\Controllers;

use App\Models\Enseignant;
use App\Models\AnneeAcademique;
use App\Models\ActivitePedagogique;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class EtatRecapitulatifController extends Controller
{
    public function index(Request $request)
    {
        $anneeActive = AnneeAcademique::active();

        $enseignants = Enseignant::with(['departement', 'user'])
            ->when($request->departement, fn($q) =>
                $q->where('departement_id', $request->departement)
            )
            ->withSum(['activites as heures_validees' => function ($q) use ($anneeActive) {
                $q->where('statut', 'valide')
                  ->when($anneeActive, fn($q2) =>
                      $q2->whereHas('cours', fn($q3) =>
                          $q3->where('annee_academique_id', $anneeActive->id)
                      )
                  );
            }], 'volume_horaire')
            ->paginate(20);

        $totalHeures = ActivitePedagogique::where('statut', 'valide')->sum('volume_horaire');
        $departements = \App\Models\Departement::orderBy('nom')->get();

        $totalEnseignants   = Enseignant::count();
        $totalCours         = \App\Models\Cours::when($anneeActive, fn($q) =>
                                    $q->where('annee_academique_id', $anneeActive->id)
                                )->count();
        $totalActivites     = ActivitePedagogique::count();
        $activitesValidees  = ActivitePedagogique::where('statut', 'valide')->count();
        $tauxValidation     = $totalActivites > 0
            ? round($activitesValidees / $totalActivites * 100)
            : 0;

        return view('etats.index', compact(
            'enseignants', 'anneeActive', 'totalHeures', 'departements',
            'totalEnseignants', 'totalCours', 'activitesValidees', 'tauxValidation'
        ));
    }

    public function exportPdf(Enseignant $enseignant)
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

        $volumeTotal  = $activites->sum('volume_horaire');
        $montantTotal = $volumeTotal * $enseignant->taux_horaire;

        $pdf = Pdf::loadView('activites.fiche-pdf', compact(
            'enseignant', 'activites', 'volumeTotal', 'montantTotal', 'anneeActive'
        ))->setPaper('a4');

        $nomFichier = 'fiche_' . str_replace(' ', '_', $enseignant->nom_complet) . '.pdf';

        return $pdf->download($nomFichier);
    }

    public function exportExcel()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\EnseignantsExport,
            'etat_global_heures.xlsx'
        );
    }
}