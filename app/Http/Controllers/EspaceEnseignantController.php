<?php

namespace App\Http\Controllers;

use App\Models\ActivitePedagogique;
use App\Models\Cours;
use App\Models\AnneeAcademique;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class EspaceEnseignantController extends Controller
{
    private function enseignant()
    {
        return auth()->user()->enseignant;
    }

    public function index()
    {
        $enseignant     = $this->enseignant();
        $anneeActive    = AnneeAcademique::active();
        $activites      = $enseignant->activites()->with('cours')->latest()->get();
        $volumeValide   = $enseignant->volumeHoraireValide($anneeActive?->id);

        $stats = [
            'cours_actifs' => $activites->pluck('cours_id')->unique()->count(),
            'en_attente'   => $activites->where('statut', 'en_attente')->count(),
            'montant_estime' => $volumeValide * $enseignant->taux_horaire,
        ];

        // Regroupement par cours pour le panneau "Mes cours"
        $mesCours = $activites->where('statut', 'valide')
            ->groupBy('cours_id')
            ->map(function ($groupe) {
                return [
                    'cours'  => $groupe->first()->cours,
                    'heures' => $groupe->sum('volume_horaire'),
                ];
            })
            ->values();

        return view('espace-enseignant.index', compact(
            'enseignant', 'activites', 'volumeValide', 'anneeActive', 'stats', 'mesCours'
        ));
    }

    public function exportFiche()
    {
        $enseignant  = $this->enseignant();
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

        return $pdf->download('ma_fiche_' . str_replace(' ', '_', $enseignant->nom_complet) . '.pdf');
    }

    public function createActivite()
    {
        $anneeActive = AnneeAcademique::active();
        $cours = $anneeActive
            ? Cours::where('annee_academique_id', $anneeActive->id)->orderBy('intitule')->get()
            : collect();

        return view('espace-enseignant.activite-create', compact('cours', 'anneeActive'));
    }

    public function storeActivite(Request $request)
    {
        $enseignant = $this->enseignant();

        $validated = $request->validate([
            'cours_id'      => 'required|exists:cours,id',
            'type_action'   => 'required|in:creation,mise_a_jour',
            'niveau_contenu'=> 'required|integer|in:1,2,3',
        ]);

        $existe = ActivitePedagogique::where('enseignant_id', $enseignant->id)
            ->where('cours_id', $validated['cours_id'])
            ->where('type_action', $validated['type_action'])
            ->exists();

        if ($existe) {
            return back()->withInput()
                ->withErrors(['erreur' => 'Vous avez déjà déclaré cette activité pour ce cours.']);
        }

        ActivitePedagogique::create(array_merge(
            $validated,
            ['enseignant_id' => $enseignant->id]
        ));

        return redirect()->route('espace.index')
            ->with('succes', 'Activité déclarée. Elle sera vérifiée par le secrétariat.');
    }

    public function editActivite(ActivitePedagogique $activite)
    {
        // Sécurité : un enseignant ne peut modifier que ses propres activités
        if ($activite->enseignant_id !== $this->enseignant()->id) {
            abort(403);
        }

        if (!$activite->estModifiable()) {
            return redirect()->route('espace.index')
                ->withErrors(['erreur' => 'Cette activité ne peut plus être modifiée.']);
        }

        $anneeActive = AnneeAcademique::active();
        $cours = Cours::where('annee_academique_id', $anneeActive?->id)->get();

        return view('espace-enseignant.activite-edit', compact('activite', 'cours'));
    }

    public function updateActivite(Request $request, ActivitePedagogique $activite)
    {
        if ($activite->enseignant_id !== $this->enseignant()->id) {
            abort(403);
        }

        if (!$activite->estModifiable()) {
            return redirect()->route('espace.index')
                ->withErrors(['erreur' => 'Cette activité ne peut plus être modifiée.']);
        }

        $validated = $request->validate([
            'cours_id'      => 'required|exists:cours,id',
            'type_action'   => 'required|in:creation,mise_a_jour',
            'niveau_contenu'=> 'required|integer|in:1,2,3',
        ]);

        // Remettre en attente après correction
        $activite->update(array_merge($validated, [
            'statut'            => 'en_attente',
            'commentaire_rejet' => null,
        ]));

        return redirect()->route('espace.index')
            ->with('succes', 'Activité mise à jour et soumise à nouveau.');
    }
}