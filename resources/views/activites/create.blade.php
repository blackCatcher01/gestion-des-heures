@extends('layouts.app')

@section('titre', 'Déclarer une activité')

@section('fil-ariane')
    <span>UVCI</span>
    <span class="separateur-ariane"><i class="bi bi-chevron-right"></i></span>
    <a href="{{ route('activites.index') }}" style="color:var(--couleur-texte-secondaire)">
        Activités
    </a>
    <span class="separateur-ariane"><i class="bi bi-chevron-right"></i></span>
    <span>Déclarer</span>
@endsection

@section('contenu')

    <div class="en-tete-page">
        <div class="groupe-titre">
            <h1 class="titre-page">Déclarer une activité pédagogique</h1>
            <p class="sous-titre-page">
                Le volume horaire est calculé automatiquement selon la formule :
                Séquences × Coefficient
            </p>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 360px;gap:20px;align-items:start">

        {{-- Formulaire --}}
        <div class="carte">
            <div class="corps-carte">
                <form method="POST" action="{{ route('activites.store') }}" id="form-activite">
                    @csrf

                    @php
                    $cs = "padding:9px 12px;border:1.5px solid var(--couleur-bordure);
                           border-radius:var(--rayon-element);font-size:13.5px;
                           font-family:var(--police);outline:none;width:100%;
                           background:#F8FAFF;transition:border-color 0.2s";
                    @endphp

                    <div style="display:flex;flex-direction:column;gap:16px">

                        <div style="display:flex;flex-direction:column;gap:5px">
                            <label style="font-size:12.5px;font-weight:600">
                                Enseignant *
                            </label>
                            <select name="enseignant_id" required
                                    style="{{ $cs }};appearance:none;cursor:pointer">
                                <option value="">Sélectionner un enseignant…</option>
                                @foreach($enseignants as $ens)
                                    <option value="{{ $ens->id }}"
                                        {{ old('enseignant_id') == $ens->id ? 'selected' : '' }}>
                                        {{ $ens->nom_complet }} ({{ $ens->departement->code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div style="display:flex;flex-direction:column;gap:5px">
                            <label style="font-size:12.5px;font-weight:600">
                                Cours concerné *
                                @if($anneeActive)
                                    <span style="font-weight:400;color:var(--couleur-texte-secondaire)">
                                        — Année {{ $anneeActive->libelle }}
                                    </span>
                                @endif
                            </label>
                            <select name="cours_id" id="select-cours" required
                                    style="{{ $cs }};appearance:none;cursor:pointer"
                                    onchange="majCalcul()">
                                <option value="">Sélectionner un cours…</option>
                                @foreach($cours as $c)
                                    <option value="{{ $c->id }}"
                                            data-sequences="{{ $c->nombre_sequences }}"
                                        {{ old('cours_id') == $c->id ? 'selected' : '' }}>
                                        {{ $c->intitule }} ({{ $c->niveau }} — {{ $c->nombre_credits }} Cr)
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px">
                            <div style="display:flex;flex-direction:column;gap:5px">
                                <label style="font-size:12.5px;font-weight:600">Type d'action *</label>
                                <select name="type_action" id="select-type" required
                                        style="{{ $cs }};appearance:none;cursor:pointer"
                                        onchange="majCalcul()">
                                    <option value="">Sélectionner…</option>
                                    <option value="creation"
                                        {{ old('type_action') === 'creation' ? 'selected' : '' }}>
                                        Création de ressources
                                    </option>
                                    <option value="mise_a_jour"
                                        {{ old('type_action') === 'mise_a_jour' ? 'selected' : '' }}>
                                        Mise à jour de ressources
                                    </option>
                                </select>
                            </div>

                            <div style="display:flex;flex-direction:column;gap:5px">
                                <label style="font-size:12.5px;font-weight:600">Niveau de contenu *</label>
                                <select name="niveau_contenu" id="select-niveau" required
                                        style="{{ $cs }};appearance:none;cursor:pointer"
                                        onchange="majCalcul()">
                                    <option value="">Sélectionner…</option>
                                    <option value="1" {{ old('niveau_contenu') == 1 ? 'selected' : '' }}>
                                        Niveau 1 — Contenus simples + quiz
                                    </option>
                                    <option value="2" {{ old('niveau_contenu') == 2 ? 'selected' : '' }}>
                                        Niveau 2 — Interactif + évaluations
                                    </option>
                                    <option value="3" {{ old('niveau_contenu') == 3 ? 'selected' : '' }}>
                                        Niveau 3 — Simulations / serious games
                                    </option>
                                </select>
                            </div>
                        </div>

                    </div>

                    <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:20px">
                        <a href="{{ route('activites.index') }}" class="btn btn-contour">
                            Annuler
                        </a>
                        <button type="submit" class="btn btn-principal">
                            <i class="bi bi-check-lg"></i>Enregistrer la déclaration
                        </button>
                    </div>

                </form>
            </div>
        </div>

        {{-- Panneau calcul en temps réel --}}
        <div class="carte">
            <div class="en-tete-carte">
                <div class="titre-carte">
                    <i class="bi bi-calculator"></i>Calcul automatique
                </div>
            </div>
            <div class="corps-carte">
                <div style="display:flex;flex-direction:column;gap:14px">

                    <div style="background:var(--couleur-fond);border-radius:9px;padding:13px">
                        <div style="font-size:11px;font-weight:700;color:var(--couleur-texte-secondaire);
                                    text-transform:uppercase;letter-spacing:0.08em;margin-bottom:4px">
                            Séquences du cours
                        </div>
                        <div id="affichage-sequences"
                             style="font-size:22px;font-weight:700;color:var(--couleur-texte-principal)">
                            —
                        </div>
                    </div>

                    <div style="text-align:center;font-size:18px;color:var(--couleur-texte-secondaire)">×</div>

                    <div style="background:var(--couleur-fond);border-radius:9px;padding:13px">
                        <div style="font-size:11px;font-weight:700;color:var(--couleur-texte-secondaire);
                                    text-transform:uppercase;letter-spacing:0.08em;margin-bottom:4px">
                            Coefficient
                        </div>
                        <div id="affichage-coefficient"
                             style="font-size:22px;font-weight:700;color:var(--couleur-texte-principal)">
                            —
                        </div>
                    </div>

                    <div style="text-align:center;font-size:18px;color:var(--couleur-texte-secondaire)">=</div>

                    <div style="background:linear-gradient(135deg,rgba(67,97,238,0.08),rgba(114,9,183,0.08));
                                border:1px solid rgba(67,97,238,0.2);border-radius:9px;padding:16px;
                                text-align:center">
                        <div style="font-size:11px;font-weight:700;color:var(--couleur-principale);
                                    text-transform:uppercase;letter-spacing:0.08em;margin-bottom:6px">
                            Volume horaire
                        </div>
                        <div id="affichage-volume"
                             style="font-size:34px;font-weight:700;color:var(--couleur-principale)">
                            — h
                        </div>
                    </div>

                    {{-- Table des coefficients --}}
                    <div style="background:var(--couleur-fond);border-radius:9px;padding:12px;
                                font-size:11.5px">
                        <div style="font-weight:700;color:var(--couleur-texte-secondaire);
                                    margin-bottom:8px;text-transform:uppercase;letter-spacing:0.07em">
                            Référence coefficients
                        </div>
                        <table style="width:100%;border-collapse:collapse">
                            <thead>
                                <tr style="color:var(--couleur-texte-secondaire)">
                                    <th style="text-align:left;padding:3px 0;font-weight:600">Type</th>
                                    <th style="text-align:center;padding:3px 0;font-weight:600">N1</th>
                                    <th style="text-align:center;padding:3px 0;font-weight:600">N2</th>
                                    <th style="text-align:center;padding:3px 0;font-weight:600">N3</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td style="padding:3px 0;color:var(--couleur-texte-secondaire)">Création</td>
                                    <td style="text-align:center;font-weight:600;color:var(--couleur-principale)">0,40</td>
                                    <td style="text-align:center;font-weight:600;color:var(--couleur-principale)">0,75</td>
                                    <td style="text-align:center;font-weight:600;color:var(--couleur-principale)">1,50</td>
                                </tr>
                                <tr>
                                    <td style="padding:3px 0;color:var(--couleur-texte-secondaire)">Mise à jour</td>
                                    <td style="text-align:center;font-weight:600;color:var(--couleur-principale)">0,20</td>
                                    <td style="text-align:center;font-weight:600;color:var(--couleur-principale)">0,375</td>
                                    <td style="text-align:center;font-weight:600;color:var(--couleur-principale)">0,75</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>

    </div>

@endsection

@push('scripts')
<script>
var COEFS = {
    'creation':    {'1': 0.40,  '2': 0.75,  '3': 1.50},
    'mise_a_jour': {'1': 0.20,  '2': 0.375, '3': 0.75}
};

function majCalcul() {
    var coursSelect  = document.getElementById('select-cours');
    var typeSelect   = document.getElementById('select-type');
    var niveauSelect = document.getElementById('select-niveau');

    var optionCours = coursSelect.options[coursSelect.selectedIndex];
    var sequences   = optionCours ? optionCours.dataset.sequences : null;
    var type        = typeSelect.value;
    var niveau      = niveauSelect.value;

    var elSeq  = document.getElementById('affichage-sequences');
    var elCoef = document.getElementById('affichage-coefficient');
    var elVol  = document.getElementById('affichage-volume');

    if (!sequences || !type || !niveau) {
        elSeq.textContent  = '—';
        elCoef.textContent = '—';
        elVol.textContent  = '— h';
        return;
    }

    var seq   = parseInt(sequences);
    var coef  = COEFS[type][niveau];
    var total = seq * coef;

    elSeq.textContent  = seq;
    elCoef.textContent = coef.toString().replace('.', ',');
    elVol.textContent  = total.toFixed(1).replace('.', ',') + ' h';
}

// Recalculer si des valeurs sont déjà sélectionnées (old())
document.addEventListener('DOMContentLoaded', majCalcul);
</script>
@endpush