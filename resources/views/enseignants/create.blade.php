@extends('layouts.app')

@section('titre', 'Ajouter un enseignant')

@section('fil-ariane')
    <span>UVCI</span>
    <span class="separateur-ariane"><i class="bi bi-chevron-right"></i></span>
    <a href="{{ route('enseignants.index') }}" style="color:var(--couleur-texte-secondaire)">
        Enseignants
    </a>
    <span class="separateur-ariane"><i class="bi bi-chevron-right"></i></span>
    <span>Ajouter</span>
@endsection

@section('contenu')

    <div class="en-tete-page">
        <div class="groupe-titre">
            <h1 class="titre-page">Ajouter un enseignant</h1>
            <p class="sous-titre-page">
                Un compte de connexion sera créé automatiquement
            </p>
        </div>
    </div>

    <div class="carte" style="max-width:700px">
        <div class="corps-carte">
            <form method="POST" action="{{ route('enseignants.store') }}">
                @csrf

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px">

                    <div style="display:flex;flex-direction:column;gap:5px">
                        <label style="font-size:12.5px;font-weight:600">Prénom *</label>
                        <input class="saisie-modal" type="text" name="prenom"
                               value="{{ old('prenom') }}" required
                               style="padding:9px 12px;border:1.5px solid var(--couleur-bordure);
                                      border-radius:var(--rayon-element);font-size:13.5px;
                                      font-family:var(--police);outline:none;width:100%;
                                      background:#F8FAFF">
                    </div>

                    <div style="display:flex;flex-direction:column;gap:5px">
                        <label style="font-size:12.5px;font-weight:600">Nom de famille *</label>
                        <input class="saisie-modal" type="text" name="nom"
                               value="{{ old('nom') }}" required
                               style="padding:9px 12px;border:1.5px solid var(--couleur-bordure);
                                      border-radius:var(--rayon-element);font-size:13.5px;
                                      font-family:var(--police);outline:none;width:100%;
                                      background:#F8FAFF">
                    </div>

                    <div style="display:flex;flex-direction:column;gap:5px;grid-column:1/-1">
                        <label style="font-size:12.5px;font-weight:600">Adresse e-mail *</label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                               style="padding:9px 12px;border:1.5px solid var(--couleur-bordure);
                                      border-radius:var(--rayon-element);font-size:13.5px;
                                      font-family:var(--police);outline:none;width:100%;
                                      background:#F8FAFF"
                               placeholder="prenom.nom@uvci.edu.ci">
                    </div>

                    <div style="display:flex;flex-direction:column;gap:5px">
                        <label style="font-size:12.5px;font-weight:600">Grade *</label>
                        <select name="grade" required
                                style="padding:9px 12px;border:1.5px solid var(--couleur-bordure);
                                       border-radius:var(--rayon-element);font-size:13.5px;
                                       font-family:var(--police);outline:none;width:100%;
                                       appearance:none;background:#F8FAFF;cursor:pointer">
                            <option value="">Sélectionner…</option>
                            <option value="assistant" {{ old('grade') === 'assistant' ? 'selected' : '' }}>
                                Assistant
                            </option>
                            <option value="maitre_assistant" {{ old('grade') === 'maitre_assistant' ? 'selected' : '' }}>
                                Maître-Assistant
                            </option>
                            <option value="maitre_conferences" {{ old('grade') === 'maitre_conferences' ? 'selected' : '' }}>
                                Maître de Conférences
                            </option>
                            <option value="professeur" {{ old('grade') === 'professeur' ? 'selected' : '' }}>
                                Professeur
                            </option>
                        </select>
                    </div>

                    <div style="display:flex;flex-direction:column;gap:5px">
                        <label style="font-size:12.5px;font-weight:600">Statut *</label>
                        <select name="statut" required
                                style="padding:9px 12px;border:1.5px solid var(--couleur-bordure);
                                       border-radius:var(--rayon-element);font-size:13.5px;
                                       font-family:var(--police);outline:none;width:100%;
                                       appearance:none;background:#F8FAFF;cursor:pointer">
                            <option value="">Sélectionner…</option>
                            <option value="permanent" {{ old('statut') === 'permanent' ? 'selected' : '' }}>
                                Permanent
                            </option>
                            <option value="vacataire" {{ old('statut') === 'vacataire' ? 'selected' : '' }}>
                                Vacataire
                            </option>
                        </select>
                    </div>

                    <div style="display:flex;flex-direction:column;gap:5px">
                        <label style="font-size:12.5px;font-weight:600">Département *</label>
                        <select name="departement_id" required
                                style="padding:9px 12px;border:1.5px solid var(--couleur-bordure);
                                       border-radius:var(--rayon-element);font-size:13.5px;
                                       font-family:var(--police);outline:none;width:100%;
                                       appearance:none;background:#F8FAFF;cursor:pointer">
                            <option value="">Sélectionner…</option>
                            @foreach($departements as $dept)
                                <option value="{{ $dept->id }}"
                                    {{ old('departement_id') == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->nom }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div style="display:flex;flex-direction:column;gap:5px">
                        <label style="font-size:12.5px;font-weight:600">
                            Taux horaire (FCFA/h) *
                        </label>
                        <input type="number" name="taux_horaire"
                               value="{{ old('taux_horaire', 4000) }}" min="0" required
                               style="padding:9px 12px;border:1.5px solid var(--couleur-bordure);
                                      border-radius:var(--rayon-element);font-size:13.5px;
                                      font-family:var(--police);outline:none;width:100%;
                                      background:#F8FAFF">
                    </div>

                    <div style="display:flex;flex-direction:column;gap:5px;grid-column:1/-1">
                        <label style="font-size:12.5px;font-weight:600">Téléphone</label>
                        <input type="tel" name="telephone"
                               value="{{ old('telephone') }}"
                               placeholder="+225 07 00 00 00 00"
                               style="padding:9px 12px;border:1.5px solid var(--couleur-bordure);
                                      border-radius:var(--rayon-element);font-size:13.5px;
                                      font-family:var(--police);outline:none;width:100%;
                                      background:#F8FAFF">
                    </div>

                </div>

                <div style="background:var(--couleur-fond);border-radius:9px;padding:12px 14px;
                            margin-bottom:18px;font-size:13px;color:var(--couleur-texte-secondaire)">
                    <i class="bi bi-info-circle" style="color:var(--couleur-principale)"></i>
                    Le compte de connexion sera créé automatiquement avec le mot de passe
                    provisoire <strong>uvci2026</strong>. L'enseignant devra le changer
                    à sa première connexion.
                </div>

                <div style="display:flex;gap:10px;justify-content:flex-end">
                    <a href="{{ route('enseignants.index') }}" class="btn btn-contour">
                        Annuler
                    </a>
                    <button type="submit" class="btn btn-principal">
                        <i class="bi bi-check-lg"></i>Enregistrer
                    </button>
                </div>

            </form>
        </div>
    </div>

@endsection