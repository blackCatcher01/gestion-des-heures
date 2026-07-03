<?php

namespace App\Http\Controllers;

use App\Models\AnneeAcademique;
use App\Models\Departement;
use App\Models\ParametreCalcul;
use App\Models\User;
use Illuminate\Http\Request;

class ParametreController extends Controller
{
    public function index()
    {
        $annees       = AnneeAcademique::orderByDesc('date_debut')->get();
        $coefficients = ParametreCalcul::orderBy('type_action')->orderBy('niveau_contenu')->get();
        $departements = Departement::withCount('enseignants')->orderBy('nom')->get();
        $utilisateurs = User::with('enseignant')->orderBy('name')->get();

        return view('parametres.index', compact(
            'annees', 'coefficients', 'departements', 'utilisateurs'
        ));
    }

    public function updateCoefficient(Request $request, ParametreCalcul $parametre)
    {
        $request->validate([
            'coefficient' => 'required|numeric|min:0|max:10',
        ]);

        $parametre->update(['coefficient' => $request->coefficient]);

        return back()->with('succes', 'Coefficient mis à jour.');
    }

    public function storeAnnee(Request $request)
    {
        $request->validate([
            'libelle'    => 'required|string|max:20',
            'date_debut' => 'required|date',
            'date_fin'   => 'required|date|after:date_debut',
        ]);

        AnneeAcademique::create($request->only('libelle', 'date_debut', 'date_fin'));

        return back()->with('succes', 'Année académique créée.');
    }

    public function activerAnnee(AnneeAcademique $annee)
    {
        // Désactiver toutes les autres années
        AnneeAcademique::where('id', '!=', $annee->id)->update(['est_active' => false]);
        $annee->update(['est_active' => true]);

        return back()->with('succes', "Année {$annee->libelle} activée.");
    }

    public function cloturerAnnee(AnneeAcademique $annee)
    {
        // Verrouiller toutes les activités validées de cette année
        \App\Models\ActivitePedagogique::whereHas('cours', fn($q) =>
            $q->where('annee_academique_id', $annee->id)
        )->where('statut', 'valide')
         ->update(['statut' => 'verrouille']);

        $annee->update(['est_active' => false]);

        return back()->with('succes', "Année {$annee->libelle} clôturée et données verrouillées.");
    }

    public function store(Request $request)
    {
        $request->validate([
            'nom'  => 'required|string|max:100',
            'code' => 'required|string|max:20|unique:departements,code',
        ]);

        Departement::create($request->only('nom', 'code'));

        return back()->with('succes', 'Département créé.');
    }

    public function update(Request $request, Departement $departement)
    {
        $request->validate([
            'nom'  => 'required|string|max:100',
            'code' => 'required|string|max:20|unique:departements,code,'.$departement->id,
        ]);

        $departement->update($request->only('nom', 'code'));

        return back()->with('succes', 'Département mis à jour.');
    }

    public function destroy(Departement $departement)
    {
        if ($departement->enseignants()->exists()) {
            return back()->withErrors([
                'erreur' => 'Impossible de supprimer un département avec des enseignants.'
            ]);
        }

        $departement->delete();
        return back()->with('succes', 'Département supprimé.');
    }
}