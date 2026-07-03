<?php

namespace App\Http\Controllers;

use App\Models\SequencePedagogique;
use App\Models\Cours;
use Illuminate\Http\Request;

class SequenceController extends Controller
{
    public function index(Request $request)
    {
        $cours = Cours::with('anneeAcademique')->orderBy('intitule')->get();

        $sequences = SequencePedagogique::with('cours')
            ->when($request->cours_id, fn($q) =>
                $q->where('cours_id', $request->cours_id)
            )
            ->when($request->recherche, fn($q) =>
                $q->where('titre', 'like', "%{$request->recherche}%")
            )
            ->orderBy('cours_id')
            ->orderBy('numero_ordre')
            ->paginate(20);

        $coursFiltreActuel = $request->cours_id
            ? Cours::find($request->cours_id)
            : null;

        return view('sequences.index', compact('sequences', 'cours', 'coursFiltreActuel'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cours_id'     => 'required|exists:cours,id',
            'titre'        => 'required|string|max:200',
            'numero_ordre' => 'required|integer|min:1',
            'description'  => 'nullable|string|max:500',
        ]);

        // Vérifier que le numéro d'ordre n'est pas déjà pris pour ce cours
        $existe = SequencePedagogique::where('cours_id', $validated['cours_id'])
            ->where('numero_ordre', $validated['numero_ordre'])
            ->exists();

        if ($existe) {
            return back()
                ->withInput()
                ->withErrors(['erreur' => "Le numéro d'ordre {$validated['numero_ordre']} est déjà utilisé pour ce cours."]);
        }

        SequencePedagogique::create($validated);

        return back()->with('succes', 'Séquence créée avec succès.');
    }

    public function update(Request $request, SequencePedagogique $sequence)
    {
        $validated = $request->validate([
            'cours_id'     => 'required|exists:cours,id',
            'titre'        => 'required|string|max:200',
            'numero_ordre' => 'required|integer|min:1',
            'description'  => 'nullable|string|max:500',
        ]);

        // Vérifier unicité en excluant la séquence actuelle
        $existe = SequencePedagogique::where('cours_id', $validated['cours_id'])
            ->where('numero_ordre', $validated['numero_ordre'])
            ->where('id', '!=', $sequence->id)
            ->exists();

        if ($existe) {
            return back()
                ->withInput()
                ->withErrors(['erreur' => "Le numéro d'ordre {$validated['numero_ordre']} est déjà utilisé pour ce cours."]);
        }

        $sequence->update($validated);

        return back()->with('succes', 'Séquence mise à jour.');
    }

    public function destroy(SequencePedagogique $sequence)
    {
        if ($sequence->ressources()->exists()) {
            return back()->withErrors([
                'erreur' => 'Impossible de supprimer une séquence contenant des ressources.'
            ]);
        }

        $sequence->delete();

        return back()->with('succes', 'Séquence supprimée.');
    }
}