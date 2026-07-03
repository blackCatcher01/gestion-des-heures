<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TableauDeBordController;
use App\Http\Controllers\EnseignantController;
use App\Http\Controllers\CoursController;
use App\Http\Controllers\SequenceController;
use App\Http\Controllers\RessourceController;
use App\Http\Controllers\ActiviteController;
use App\Http\Controllers\ValidationController;
use App\Http\Controllers\CalculHeuresController;
use App\Http\Controllers\EtatRecapitulatifController;
use App\Http\Controllers\ParametreController;
use App\Http\Controllers\EspaceEnseignantController;

// Redirection racine vers login
Route::get('/', function () {
    return redirect()->route('login');
});

// ─── Routes authentifiées ────────────────────────────────────────
Route::middleware(['auth'])->group(function () {

    // Tableau de bord (Admin + Secrétaire)
    Route::middleware(['role:admin,secretaire'])->group(function () {
        Route::get('/tableau-de-bord', [TableauDeBordController::class, 'index'])
             ->name('tableau-de-bord');

        // Enseignants
        Route::resource('enseignants', EnseignantController::class);

        // Cours
        Route::resource('cours', CoursController::class);

        // Séquences
        Route::resource('sequences', SequenceController::class);

        // Ressources pédagogiques
        Route::resource('ressources', RessourceController::class);

        // Activités (saisie secrétaire + liste)
        Route::get('/activites', [ActiviteController::class, 'index'])
             ->name('activites.index');
        Route::get('/activites/creer', [ActiviteController::class, 'create'])
             ->name('activites.create');
        Route::post('/activites', [ActiviteController::class, 'store'])
             ->name('activites.store');
        Route::get('/activites/{activite}', [ActiviteController::class, 'show'])
             ->name('activites.show');
        Route::delete('/activites/{activite}', [ActiviteController::class, 'destroy'])
             ->name('activites.destroy');

        // Validations
        Route::get('/validations', [ValidationController::class, 'index'])
             ->name('validations.index');
        Route::post('/validations/{activite}/valider', [ValidationController::class, 'valider'])
             ->name('validations.valider');
        Route::post('/validations/{activite}/rejeter', [ValidationController::class, 'rejeter'])
             ->name('validations.rejeter');
        Route::post('/validations/valider-tout', [ValidationController::class, 'validerTout'])
             ->name('validations.valider-tout');

        // Calcul des heures
        Route::get('/calcul-heures', [CalculHeuresController::class, 'index'])
             ->name('calcul-heures.index');
        Route::get('/calcul-heures/{enseignant}', [CalculHeuresController::class, 'detail'])
             ->name('calcul-heures.detail');

        // États récapitulatifs
        Route::get('/etats', [EtatRecapitulatifController::class, 'index'])
             ->name('etats.index');
        Route::get('/etats/{enseignant}/pdf', [EtatRecapitulatifController::class, 'exportPdf'])
             ->name('etats.pdf');
        Route::get('/etats/global/excel', [EtatRecapitulatifController::class, 'exportExcel'])
             ->name('etats.excel');
    });

    // Paramètres (Admin seulement)
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/parametres', [ParametreController::class, 'index'])
             ->name('parametres.index');
        Route::put('/parametres/coefficients/{parametre}', [ParametreController::class, 'updateCoefficient'])
             ->name('parametres.coefficients.update');
        Route::resource('parametres/departements', ParametreController::class)
             ->only(['store', 'update', 'destroy'])
             ->names(['store' => 'departements.store', 'update' => 'departements.update',
                      'destroy' => 'departements.destroy']);
        Route::post('/parametres/annees', [ParametreController::class, 'storeAnnee'])
             ->name('annees.store');
        Route::post('/parametres/annees/{annee}/activer', [ParametreController::class, 'activerAnnee'])
             ->name('annees.activer');
        Route::post('/parametres/annees/{annee}/cloturer', [ParametreController::class, 'cloturerAnnee'])
             ->name('annees.cloturer');
    });

    // Espace enseignant (Enseignant seulement)
    Route::middleware(['role:enseignant'])->group(function () {
        Route::get('/mon-espace', [EspaceEnseignantController::class, 'index'])
             ->name('espace.index');
        Route::get('/mon-espace/activites/creer', [EspaceEnseignantController::class, 'createActivite'])
             ->name('espace.activites.create');
        Route::post('/mon-espace/activites', [EspaceEnseignantController::class, 'storeActivite'])
             ->name('espace.activites.store');
        Route::get('/mon-espace/activites/{activite}/modifier', [EspaceEnseignantController::class, 'editActivite'])
             ->name('espace.activites.edit');
        Route::put('/mon-espace/activites/{activite}', [EspaceEnseignantController::class, 'updateActivite'])
             ->name('espace.activites.update');
        Route::get('/mon-espace/fiche/pdf', [EspaceEnseignantController::class, 'exportFiche'])
             ->name('espace.fiche.pdf');
    });

    // Redirection post-login selon le rôle
    Route::get('/redirect', function () {
        return match(auth()->user()->role) {
            'admin', 'secretaire' => redirect()->route('tableau-de-bord'),
            'enseignant'          => redirect()->route('espace.index'),
            default               => redirect()->route('login'),
        };
    })->name('redirect');
});

// Routes d'authentification générées par Breeze
require __DIR__.'/auth.php';