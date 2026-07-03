<?php

namespace App\Http\Controllers;

use App\Models\Enseignant;
use App\Models\Departement;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class EnseignantController extends Controller
{
    public function index(Request $request)
    {
        $enseignants = Enseignant::with(['departement', 'user'])
            ->when($request->recherche, fn($q) =>
                $q->where('nom', 'like', "%{$request->recherche}%")
                  ->orWhere('prenom', 'like', "%{$request->recherche}%")
            )
            ->when($request->statut, fn($q) =>
                $q->where('statut', $request->statut)
            )
            ->when($request->departement, fn($q) =>
                $q->where('departement_id', $request->departement)
            )
            ->paginate(15);

        $departements = Departement::orderBy('nom')->get();

        $totalEnseignants = Enseignant::count();
        $stats = [
            'total'       => $totalEnseignants,
            'permanents'  => Enseignant::where('statut', 'permanent')->count(),
            'vacataires'  => Enseignant::where('statut', 'vacataire')->count(),
            'en_attente'  => \App\Models\ActivitePedagogique::where('statut', 'en_attente')->count(),
        ];

        return view('enseignants.index', compact('enseignants', 'departements', 'stats'));
    }

    public function create()
    {
        $departements = Departement::orderBy('nom')->get();
        return view('enseignants.create', compact('departements'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nom'            => 'required|string|max:100',
            'prenom'         => 'required|string|max:100',
            'email'          => 'required|email|unique:users,email',
            'grade'          => 'required|in:assistant,maitre_assistant,maitre_conferences,professeur',
            'statut'         => 'required|in:permanent,vacataire',
            'taux_horaire'   => 'required|integer|min:0',
            'telephone'      => 'nullable|string|max:20',
            'departement_id' => 'required|exists:departements,id',
        ]);

        // Créer le compte utilisateur
        $user = User::create([
            'name'     => $validated['prenom'] . ' ' . $validated['nom'],
            'email'    => $validated['email'],
            'password' => Hash::make('uvci2026'),   // Mot de passe provisoire
            'role'     => 'enseignant',
            'actif'    => true,
        ]);

        // Créer le profil enseignant
        Enseignant::create([
            'nom'            => $validated['nom'],
            'prenom'         => $validated['prenom'],
            'grade'          => $validated['grade'],
            'statut'         => $validated['statut'],
            'taux_horaire'   => $validated['taux_horaire'],
            'telephone'      => $validated['telephone'],
            'user_id'        => $user->id,
            'departement_id' => $validated['departement_id'],
        ]);

        return redirect()->route('enseignants.index')
            ->with('succes', 'Enseignant enregistré. Mot de passe provisoire : uvci2026');
    }

    public function show(Enseignant $enseignant)
    {
        $enseignant->load(['departement', 'user', 'activites.cours']);
        $volumeTotal = $enseignant->volumeHoraireValide();
        return view('enseignants.show', compact('enseignant', 'volumeTotal'));
    }

    public function edit(Enseignant $enseignant)
    {
        $departements = Departement::orderBy('nom')->get();
        return view('enseignants.edit', compact('enseignant', 'departements'));
    }

    public function update(Request $request, Enseignant $enseignant)
    {
        $validated = $request->validate([
            'nom'            => 'required|string|max:100',
            'prenom'         => 'required|string|max:100',
            'grade'          => 'required|in:assistant,maitre_assistant,maitre_conferences,professeur',
            'statut'         => 'required|in:permanent,vacataire',
            'taux_horaire'   => 'required|integer|min:0',
            'telephone'      => 'nullable|string|max:20',
            'departement_id' => 'required|exists:departements,id',
        ]);

        $enseignant->update($validated);
        $enseignant->user->update([
            'name' => $validated['prenom'] . ' ' . $validated['nom'],
        ]);

        return redirect()->route('enseignants.index')
            ->with('succes', 'Enseignant mis à jour.');
    }

    public function destroy(Enseignant $enseignant)
    {
        // Vérifier qu'il n'a pas d'activités validées
        if ($enseignant->activites()->where('statut', 'valide')->exists()) {
            return back()->withErrors([
                'erreur' => 'Impossible de supprimer un enseignant avec des activités validées.'
            ]);
        }

        $enseignant->user->delete(); // Supprime aussi l'enseignant (cascade)
        return redirect()->route('enseignants.index')
            ->with('succes', 'Enseignant supprimé.');
    }
}