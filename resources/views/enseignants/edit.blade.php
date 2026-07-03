{{-- ================================================================
     FICHIER : resources/views/enseignants/edit.blade.php
     ================================================================ --}}
@extends('layouts.app')

@section('titre', 'Modifier — '.$enseignant->nom_complet)

@section('fil-ariane')
    <span>UVCI</span>
    <span class="separateur-ariane"><i class="bi bi-chevron-right"></i></span>
    <a href="{{ route('enseignants.index') }}" style="color:var(--couleur-texte-secondaire)">Enseignants</a>
    <span class="separateur-ariane"><i class="bi bi-chevron-right"></i></span>
    <span>Modifier</span>
@endsection

@section('contenu')

    <div class="en-tete-page">
        <div class="groupe-titre">
            <h1 class="titre-page">Modifier — {{ $enseignant->nom_complet }}</h1>
        </div>
    </div>

    <div class="carte" style="max-width:700px">
        <div class="corps-carte">
            <form method="POST" action="{{ route('enseignants.update', $enseignant) }}">
                @csrf
                @method('PUT')

                @php
                $cs = "padding:9px 12px;border:1.5px solid var(--couleur-bordure);
                       border-radius:var(--rayon-element);font-size:13.5px;
                       font-family:var(--police);outline:none;width:100%;background:#F8FAFF";
                @endphp

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px">

                    <div style="display:flex;flex-direction:column;gap:5px">
                        <label style="font-size:12.5px;font-weight:600">Prénom *</label>
                        <input type="text" name="prenom"
                               value="{{ old('prenom', $enseignant->prenom) }}" required
                               style="{{ $cs }}">
                    </div>

                    <div style="display:flex;flex-direction:column;gap:5px">
                        <label style="font-size:12.5px;font-weight:600">Nom *</label>
                        <input type="text" name="nom"
                               value="{{ old('nom', $enseignant->nom) }}" required
                               style="{{ $cs }}">
                    </div>

                    <div style="display:flex;flex-direction:column;gap:5px">
                        <label style="font-size:12.5px;font-weight:600">Grade *</label>
                        <select name="grade" required style="{{ $cs }};appearance:none;cursor:pointer">
                            @foreach([
                                'assistant'          => 'Assistant',
                                'maitre_assistant'   => 'Maître-Assistant',
                                'maitre_conferences' => 'Maître de Conférences',
                                'professeur'         => 'Professeur',
                            ] as $val => $lab)
                                <option value="{{ $val }}"
                                    {{ old('grade',$enseignant->grade) === $val ? 'selected' : '' }}>
                                    {{ $lab }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div style="display:flex;flex-direction:column;gap:5px">
                        <label style="font-size:12.5px;font-weight:600">Statut *</label>
                        <select name="statut" required style="{{ $cs }};appearance:none;cursor:pointer">
                            <option value="permanent"
                                {{ old('statut',$enseignant->statut) === 'permanent' ? 'selected' : '' }}>
                                Permanent
                            </option>
                            <option value="vacataire"
                                {{ old('statut',$enseignant->statut) === 'vacataire' ? 'selected' : '' }}>
                                Vacataire
                            </option>
                        </select>
                    </div>

                    <div style="display:flex;flex-direction:column;gap:5px">
                        <label style="font-size:12.5px;font-weight:600">Département *</label>
                        <select name="departement_id" required style="{{ $cs }};appearance:none;cursor:pointer">
                            @foreach($departements as $dept)
                                <option value="{{ $dept->id }}"
                                    {{ old('departement_id',$enseignant->departement_id) == $dept->id ? 'selected' : '' }}>
                                    {{ $dept->nom }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div style="display:flex;flex-direction:column;gap:5px">
                        <label style="font-size:12.5px;font-weight:600">Taux horaire (FCFA/h) *</label>
                        <input type="number" name="taux_horaire" min="0"
                               value="{{ old('taux_horaire', $enseignant->taux_horaire) }}" required
                               style="{{ $cs }}">
                    </div>

                    <div style="display:flex;flex-direction:column;gap:5px;grid-column:1/-1">
                        <label style="font-size:12.5px;font-weight:600">Téléphone</label>
                        <input type="tel" name="telephone"
                               value="{{ old('telephone', $enseignant->telephone) }}"
                               style="{{ $cs }}" placeholder="+225 07 00 00 00 00">
                    </div>

                </div>

                <div style="display:flex;gap:10px;justify-content:flex-end">
                    <a href="{{ route('enseignants.index') }}" class="btn btn-contour">Annuler</a>
                    <button type="submit" class="btn btn-principal">
                        <i class="bi bi-check-lg"></i>Enregistrer les modifications
                    </button>
                </div>

            </form>
        </div>
    </div>

@endsection