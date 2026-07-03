<?php

namespace App\Http\Controllers;

use App\Models\ActivitePedagogique;
use Illuminate\Http\Request;

class ValidationController extends Controller
{
    public function index()
    {
        $enAttente = ActivitePedagogique::with(['enseignant', 'cours'])
            ->where('statut', 'en_attente')
            ->latest() 
            ->get();

        $validees = ActivitePedagogique::with(['enseignant', 'cours', 'validateur'])
            ->where('statut', 'valide')
            ->latest()
            ->take(20)
            ->get();

        $rejetees = ActivitePedagogique::with(['enseignant', 'cours'])
            ->where('statut', 'rejete')
            ->latest()
            ->take(20)
            ->get();

        $stats = [
            'en_attente'          => $enAttente->count(),
            'validees_aujourdhui' => ActivitePedagogique::where('statut', 'valide')
                                        ->whereDate('date_validation', today())->count(),
            'rejetees_total'      => ActivitePedagogique::where('statut', 'rejete')->count(),
            'heures_en_attente'   => $enAttente->sum('volume_horaire'),
        ];

        return view('validations.index', compact('enAttente', 'validees', 'rejetees', 'stats'));
    }

    public function valider(ActivitePedagogique $activite)
    {
        if ($activite->statut !== 'en_attente') {
            return back()->withErrors(['erreur' => 'Cette activité ne peut pas être validée.']);
        }

        $activite->update([
            'statut'          => 'valide',
            'date_validation' => now(),
            'validateur_id'   => auth()->id(),
        ]);

        return back()->with('succes', 'Activité validée — ' . $activite->enseignant->nom_complet);
    }

    public function rejeter(Request $request, ActivitePedagogique $activite)
    {
        $request->validate([
            'commentaire_rejet' => 'required|string|min:10|max:500',
        ]);

        if ($activite->statut !== 'en_attente') {
            return back()->withErrors(['erreur' => 'Cette activité ne peut pas être rejetée.']);
        }

        $activite->update([
            'statut'             => 'rejete',
            'commentaire_rejet'  => $request->commentaire_rejet,
            'validateur_id'      => auth()->id(),
        ]);

        return back()->with('succes', 'Activité rejetée avec motif.');
    }

    public function validerTout(Request $request)
    {
        $nb = ActivitePedagogique::where('statut', 'en_attente')
            ->update([
                'statut'          => 'valide',
                'date_validation' => now(),
                'validateur_id'   => auth()->id(),
            ]);

        return back()->with('succes', "{$nb} activité(s) validée(s).");
    }
}