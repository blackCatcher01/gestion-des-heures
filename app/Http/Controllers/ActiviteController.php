<?php

namespace App\Http\Controllers;

use App\Models\ActivitePedagogique;
use App\Models\Enseignant;
use App\Models\Cours;
use App\Models\AnneeAcademique;
use Illuminate\Http\Request;

class ActiviteController extends Controller
{
    public function index(Request $request)
    {
        $activites = ActivitePedagogique::with(['enseignant', 'cours', 'validateur'])
            ->when($request->statut, fn($q) =>
                $q->where('statut', $request->statut)
            )
            ->when($request->type_action, fn($q) =>
                $q->where('type_action', $request->type_action)
            )
            ->latest()
            ->paginate(20);

        $stats = [
            'total'      => ActivitePedagogique::count(),
            'en_attente' => ActivitePedagogique::where('statut', 'en_attente')->count(),
            'valide'     => ActivitePedagogique::where('statut', 'valide')->count(),
            'rejete'     => ActivitePedagogique::where('statut', 'rejete')->count(),
        ];

        return view('activites.index', compact('activites', 'stats'));
    }

    public function create()
    {
        $enseignants = Enseignant::with('user')->orderBy('nom')->get();
        $anneeActive = AnneeAcademique::active();
        $cours = $anneeActive
            ? Cours::where('annee_academique_id', $anneeActive->id)->orderBy('intitule')->get()
            : collect();

        return view('activites.create', compact('enseignants', 'cours', 'anneeActive'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'enseignant_id' => 'required|exists:enseignants,id',
            'cours_id'      => 'required|exists:cours,id',
            'type_action'   => 'required|in:creation,mise_a_jour',
            'niveau_contenu'=> 'required|integer|in:1,2,3',
        ]);

        // Vérifier l'unicité métier
        $existe = ActivitePedagogique::where('enseignant_id', $validated['enseignant_id'])
            ->where('cours_id', $validated['cours_id'])
            ->where('type_action', $validated['type_action'])
            ->exists();

        if ($existe) {
            return back()
                ->withInput()
                ->withErrors(['erreur' => 'Cette activité a déjà été déclarée pour cet enseignant et ce cours.']);
        }

        // Le volume_horaire est calculé automatiquement par le modèle (booted)
        ActivitePedagogique::create($validated);

        return redirect()->route('activites.index')
            ->with('succes', 'Activité déclarée. En attente de validation.');
    }

    public function show(ActivitePedagogique $activite)
    {
        $activite->load(['enseignant.departement', 'cours', 'validateur']);
        return view('activites.show', compact('activite'));
    }

    public function destroy(ActivitePedagogique $activite)
    {
        if (!$activite->estModifiable()) {
            return back()->withErrors([
                'erreur' => 'Impossible de supprimer une activité validée ou verrouillée.'
            ]);
        }

        $activite->delete();
        return redirect()->route('activites.index')
            ->with('succes', 'Activité supprimée.');
    }
}