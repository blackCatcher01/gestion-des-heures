<?php

namespace App\Http\Controllers;

use App\Models\Enseignant;
use App\Models\Cours;
use App\Models\ActivitePedagogique;
use App\Models\AnneeAcademique;
use App\Models\Departement;

class TableauDeBordController extends Controller
{
    public function index()
    {
        $anneeActive = AnneeAcademique::active();

        $stats = [
            'total_enseignants'  => Enseignant::count(),
            'total_cours'        => Cours::when($anneeActive, fn($q) =>
                                        $q->where('annee_academique_id', $anneeActive->id)
                                    )->count(),
            'heures_calculees'   => ActivitePedagogique::where('statut', 'valide')
                                        ->sum('volume_horaire'),
            'en_attente'         => ActivitePedagogique::where('statut', 'en_attente')
                                        ->count(),
        ];

        $activitesRecentes = ActivitePedagogique::with(['enseignant', 'cours'])
            ->latest()
            ->take(5)
            ->get();

        $validationsEnAttente = ActivitePedagogique::with(['enseignant', 'cours'])
            ->where('statut', 'en_attente')
            ->latest()
            ->take(4)
            ->get();

        // Heures validées par département (pour le graphique)
        $heuresParDepartement = Departement::withSum(['enseignants as heures' => function ($q) {
                $q->join('activites_pedagogiques', 'activites_pedagogiques.enseignant_id', '=', 'enseignants.id')
                  ->where('activites_pedagogiques.statut', 'valide');
            }], 'activites_pedagogiques.volume_horaire')
            ->get()
            ->map(fn($d) => ['nom' => $d->nom, 'heures' => (float) ($d->heures ?? 0)]);

        // Répartition permanents / vacataires
        $totalEnseignants = max($stats['total_enseignants'], 1);
        $nbPermanents = Enseignant::where('statut', 'permanent')->count();
        $nbVacataires = Enseignant::where('statut', 'vacataire')->count();
        $repartitionStatuts = [
            'permanents' => round($nbPermanents / $totalEnseignants * 100),
            'vacataires' => round($nbVacataires / $totalEnseignants * 100),
        ];

        // Répartition des heures validées par niveau de contenu
        $totalHeuresValidees = max((float) $stats['heures_calculees'], 1);
        $repartitionNiveaux = [];
        foreach ([1, 2, 3] as $niveau) {
            $heuresNiveau = ActivitePedagogique::where('statut', 'valide')
                ->where('niveau_contenu', $niveau)
                ->sum('volume_horaire');
            $repartitionNiveaux[$niveau] = round($heuresNiveau / $totalHeuresValidees * 100);
        }

        return view('tableau-de-bord', compact(
            'stats', 'activitesRecentes', 'validationsEnAttente', 'anneeActive',
            'heuresParDepartement', 'repartitionStatuts', 'repartitionNiveaux'
        ));
    }
}