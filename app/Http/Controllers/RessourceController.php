<?php

namespace App\Http\Controllers;

use App\Models\RessourcePedagogique;
use App\Models\SequencePedagogique;
use App\Models\Cours;
use Illuminate\Http\Request;

class RessourceController extends Controller
{
    public function index(Request $request)
    {
        $cours = Cours::with('anneeAcademique')->orderBy('intitule')->get();

        $sequences = $request->cours_id
            ? SequencePedagogique::where('cours_id', $request->cours_id)
                ->orderBy('numero_ordre')->get()
            : collect();

        $ressources = RessourcePedagogique::with(['sequence.cours'])
            ->when($request->cours_id, fn($q) =>
                $q->whereHas('sequence', fn($q2) =>
                    $q2->where('cours_id', $request->cours_id)
                )
            )
            ->when($request->sequence_id, fn($q) =>
                $q->where('sequence_id', $request->sequence_id)
            )
            ->when($request->type, fn($q) =>
                $q->where('type', $request->type)
            )
            ->orderBy('sequence_id')
            ->paginate(20);

        $totalRessources = RessourcePedagogique::count();
        $nbPdf = RessourcePedagogique::where('type', 'pdf')->count();
        $nbVideo = RessourcePedagogique::where('type', 'video')->count();
        $nbQuizEval = RessourcePedagogique::whereIn('type', ['quiz', 'evaluation'])->count();
        $diviseur = max($totalRessources, 1);

        $statsRessources = [
            'pdf' => $nbPdf,
            'video' => $nbVideo,
            'quiz_eval' => $nbQuizEval,
            'pourcentage_pdf' => round($nbPdf / $diviseur * 100),
            'pourcentage_video' => round($nbVideo / $diviseur * 100),
            'pourcentage_quiz_eval' => round($nbQuizEval / $diviseur * 100),
        ];

        return view('ressources.index', compact(
            'ressources', 'cours', 'sequences', 'statsRessources'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'sequence_id' => 'required|exists:sequences_pedagogiques,id',
            'titre'       => 'required|string|max:200',
            'type'        => 'required|in:pdf,video,quiz,interactif,evaluation',
            'url_moodle'  => 'nullable|url|max:500',
        ]);

        RessourcePedagogique::create($validated);

        return back()->with('succes', 'Ressource ajoutée avec succès.');
    }

    public function update(Request $request, RessourcePedagogique $ressource)
    {
        $validated = $request->validate([
            'sequence_id' => 'required|exists:sequences_pedagogiques,id',
            'titre'       => 'required|string|max:200',
            'type'        => 'required|in:pdf,video,quiz,interactif,evaluation',
            'url_moodle'  => 'nullable|url|max:500',
        ]);

        $ressource->update($validated);

        return back()->with('succes', 'Ressource mise à jour.');
    }

    public function destroy(RessourcePedagogique $ressource)
    {
        $ressource->delete();
        return back()->with('succes', 'Ressource supprimée.');
    }
}