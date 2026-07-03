<?php

namespace App\Http\Controllers;

use App\Models\Cours;
use App\Models\AnneeAcademique;
use Illuminate\Http\Request;

class CoursController extends Controller
{
    public function index(Request $request)
    {
        $cours = Cours::with('anneeAcademique')
            ->when($request->recherche, fn($q) =>
                $q->where('intitule', 'like', "%{$request->recherche}%")
            )
            ->when($request->niveau, fn($q) =>
                $q->where('niveau', $request->niveau)
            )
            ->when($request->annee, fn($q) =>
                $q->where('annee_academique_id', $request->annee)
            )
            ->orderBy('intitule')
            ->paginate(15);

        $annees = AnneeAcademique::orderByDesc('date_debut')->get();

        $stats = [
            'total'   => Cours::count(),
            'l1'      => Cours::where('niveau', 'L1')->count(),
            'l2'      => Cours::where('niveau', 'L2')->count(),
            'l3_plus' => Cours::whereIn('niveau', ['L3', 'M1', 'M2'])->count(),
        ];

        return view('cours.index', compact('cours', 'annees', 'stats'));
    }

    public function create()
    {
        $annees = AnneeAcademique::orderByDesc('date_debut')->get();
        return view('cours.create', compact('annees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'intitule'            => 'required|string|max:200',
            'filiere'             => 'required|string|max:50',
            'niveau'              => 'required|in:L1,L2,L3,M1,M2',
            'semestre'            => 'required|in:1,2',
            'nombre_credits'      => 'required|integer|in:1,2,3,4',
            'annee_academique_id' => 'required|exists:annees_academiques,id',
        ]);

        Cours::create($validated);

        return redirect()->route('cours.index')
            ->with('succes', 'Cours créé avec succès.');
    }

    public function edit(Cours $cour)
    {
        $annees = AnneeAcademique::orderByDesc('date_debut')->get();
        return view('cours.edit', ['cours' => $cour, 'annees' => $annees]);
    }

    public function update(Request $request, Cours $cour)
    {
        $validated = $request->validate([
            'intitule'            => 'required|string|max:200',
            'filiere'             => 'required|string|max:50',
            'niveau'              => 'required|in:L1,L2,L3,M1,M2',
            'semestre'            => 'required|in:1,2',
            'nombre_credits'      => 'required|integer|in:1,2,3,4',
            'annee_academique_id' => 'required|exists:annees_academiques,id',
        ]);

        $cour->update($validated);

        return redirect()->route('cours.index')
            ->with('succes', 'Cours mis à jour.');
    }

    public function destroy(Cours $cour)
    {
        if ($cour->activites()->exists()) {
            return back()->withErrors([
                'erreur' => 'Impossible de supprimer un cours avec des activités déclarées.'
            ]);
        }

        $cour->delete();
        return redirect()->route('cours.index')
            ->with('succes', 'Cours supprimé.');
    }
}