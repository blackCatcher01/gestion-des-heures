@extends('layouts.app')

@section('titre', 'Déclarer une activité')

@section('fil-ariane')
    <span>UVCI</span>
    <span class="separateur-ariane"><i class="bi bi-chevron-right"></i></span>
    <a href="{{ route('espace.index') }}" style="color:var(--couleur-texte-secondaire)">Mon espace</a>
    <span class="separateur-ariane"><i class="bi bi-chevron-right"></i></span>
    <span>Déclarer une activité</span>
@endsection

@section('contenu')

    <div class="en-tete-page">
        <div class="groupe-titre">
            <h1 class="titre-page">Déclarer une activité pédagogique</h1>
            <p class="sous-titre-page">
                Votre déclaration sera soumise au secrétariat pour validation.
                Le volume horaire est calculé automatiquement.
            </p>
        </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 340px;gap:20px;align-items:start">

        {{-- Formulaire --}}
        <div class="carte">
            <div class="en-tete-carte">
                <div class="titre-carte">
                    <i class="bi bi-plus-circle"></i>Nouvelle déclaration
                </div>
            </div>
            <div class="corps-carte">
                <form method="POST" action="{{ route('espace.activites.store') }}" id="form-declaration">
                    @csrf

                    @php
                    $cs = "padding:9px 12px;border:1.5px solid var(--couleur-bordure);
                           border-radius:var(--rayon-element);font-size:13.5px;
                           font-family:var(--police);outline:none;width:100%;
                           background:#F8FAFF;transition:border-color 0.2s";
                    @endphp

                    {{-- Cours --}}
                    <div style="margin-bottom:16px">
                        <label style="font-size:12.5px;font-weight:600;display:block;margin-bottom:5px">
                            Cours concerné *
                            @if($anneeActive)
                                <span style="font-weight:400;color:var(--couleur-texte-secondaire)">
                                    — Année {{ $anneeActive->libelle }}
                                </span>
                            @endif
                        </label>
                        @if($cours->isEmpty())
                            <div style="padding:12px;background:#FEF5E0;border-radius:9px;
                                        font-size:13px;color:#92610A;display:flex;gap:8px">
                                <i class="bi bi-exclamation-triangle"></i>
                                Aucun cours disponible pour cette année académique.
                                Contactez le secrétariat.
                            </div>
                        @else
                            <select name="cours_id" id="select-cours" required
                                    style="{{ $cs }};appearance:none;cursor:pointer"
                                    onchange="majCalcul()">
                                <option value="">Sélectionner un cours…</option>
                                @foreach($cours as $c)
                                    <option value="{{ $c->id }}"
                                            data-sequences="{{ $c->nombre_sequences }}"
                                        {{ old('cours_id') == $c->id ? 'selected' : '' }}>
                                        {{ $c->intitule }}
                                        ({{ $c->niveau }} — {{ $c->nombre_credits }} crédit(s))
                                    </option>
                                @endforeach
                            </select>
                        @endif
                    </div>

                    {{-- Type + Niveau --}}
                    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:20px">
                        <div>
                            <label style="font-size:12.5px;font-weight:600;display:block;margin-bottom:5px">
                                Type d'action *
                            </label>
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
                        <div>
                            <label style="font-size:12.5px;font-weight:600;display:block;margin-bottom:5px">
                                Niveau de contenu *
                            </label>
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

                    {{-- Alerte vérification Moodle --}}
                    <div style="background:#E6F7FE;border:1px solid #4CC9F0;border-radius:9px;
                                padding:12px 14px;margin-bottom:20px;font-size:13px;color:#1B6FA8;
                                display:flex;gap:10px;align-items:flex-start">
                        <i class="bi bi-info-circle" style="flex-shrink:0;margin-top:1px"></i>
                        <div>
                            <strong>Avant de soumettre :</strong> assurez-vous que les ressources
                            déclarées sont bien présentes et accessibles sur Moodle. Le secrétariat
                            effectuera une vérification avant validation.
                        </div>
                    </div>

                    <div style="display:flex;gap:10px;justify-content:flex-end">
                        <a href="{{ route('espace.index') }}" class="btn btn-contour">
                            Annuler
                        </a>
                        <button type="submit" class="btn btn-principal"
                                {{ $cours->isEmpty() ? 'disabled' : '' }}>
                            <i class="bi bi-send"></i>Soumettre la déclaration
                        </button>
                    </div>

                </form>
            </div>
        </div>

        {{-- Panneau latéral --}}
        <div>
            {{-- Calcul en temps réel --}}
            <div class="carte" style="margin-bottom:16px">
                <div class="en-tete-carte">
                    <div class="titre-carte">
                        <i class="bi bi-calculator"></i>Volume calculé
                    </div>
                </div>
                <div class="corps-carte">
                    <div style="display:flex;flex-direction:column;gap:12px">
                        <div style="background:var(--couleur-fond);border-radius:9px;padding:12px;
                                    display:flex;justify-content:space-between;align-items:center">
                            <span style="font-size:12px;color:var(--couleur-texte-secondaire)">
                                Séquences
                            </span>
                            <strong id="aff-seq" style="font-size:16px">—</strong>
                        </div>
                        <div style="text-align:center;color:var(--couleur-texte-secondaire)">×</div>
                        <div style="background:var(--couleur-fond);border-radius:9px;padding:12px;
                                    display:flex;justify-content:space-between;align-items:center">
                            <span style="font-size:12px;color:var(--couleur-texte-secondaire)">
                                Coefficient
                            </span>
                            <strong id="aff-coef" style="font-size:16px">—</strong>
                        </div>
                        <div style="text-align:center;color:var(--couleur-texte-secondaire)">=</div>
                        <div style="background:linear-gradient(135deg,rgba(67,97,238,0.08),
                                    rgba(114,9,183,0.08));border:1px solid rgba(67,97,238,0.2);
                                    border-radius:9px;padding:16px;text-align:center">
                            <div style="font-size:11px;font-weight:700;color:var(--couleur-principale);
                                        text-transform:uppercase;letter-spacing:0.07em;margin-bottom:4px">
                                Volume horaire
                            </div>
                            <div id="aff-volume"
                                 style="font-size:32px;font-weight:700;color:var(--couleur-principale)">
                                — h
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Workflow --}}
            <div class="carte">
                <div class="en-tete-carte">
                    <div class="titre-carte">
                        <i class="bi bi-arrow-right-circle"></i>Processus de validation
                    </div>
                </div>
                <div class="corps-carte">
                    <div style="display:flex;flex-direction:column;gap:12px">
                        @foreach([
                            ['Vous déclarez l\'activité', 'bi-pencil', 'var(--couleur-principale)', true],
                            ['Statut : En attente', 'bi-hourglass', 'var(--couleur-avertissement)', false],
                            ['Le secrétariat vérifie sur Moodle', 'bi-search', 'var(--couleur-texte-secondaire)', false],
                            ['Validation → heures comptabilisées', 'bi-check-circle', 'var(--couleur-succes)', false],
                        ] as [$label, $icon, $color, $actif])
                            <div style="display:flex;align-items:center;gap:10px">
                                <div style="width:32px;height:32px;border-radius:50%;
                                            background:{{ $actif ? $color : 'var(--couleur-fond)' }};
                                            display:flex;align-items:center;justify-content:center;
                                            flex-shrink:0">
                                    <i class="bi {{ $icon }}"
                                       style="font-size:15px;color:{{ $actif ? '#fff' : $color }}"></i>
                                </div>
                                <span style="font-size:12.5px;
                                             color:{{ $actif ? 'var(--couleur-texte-principal)' : 'var(--couleur-texte-secondaire)' }};
                                             font-weight:{{ $actif ? '600' : '400' }}">
                                    {{ $label }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

    </div>

@endsection

@push('scripts')
<script>
var COEFS = {
    'creation':    {'1': 0.40, '2': 0.75,  '3': 1.50},
    'mise_a_jour': {'1': 0.20, '2': 0.375, '3': 0.75}
};

function majCalcul() {
    var sel    = document.getElementById('select-cours');
    var option = sel ? sel.options[sel.selectedIndex] : null;
    var seq    = option ? option.dataset.sequences : null;
    var type   = document.getElementById('select-type').value;
    var niveau = document.getElementById('select-niveau').value;

    if (!seq || !type || !niveau) {
        document.getElementById('aff-seq').textContent    = '—';
        document.getElementById('aff-coef').textContent   = '—';
        document.getElementById('aff-volume').textContent = '— h';
        return;
    }

    var coef  = COEFS[type][niveau];
    var total = parseInt(seq) * coef;

    document.getElementById('aff-seq').textContent    = seq;
    document.getElementById('aff-coef').textContent   = coef.toString().replace('.', ',');
    document.getElementById('aff-volume').textContent = total.toFixed(1).replace('.', ',') + ' h';
}

document.addEventListener('DOMContentLoaded', majCalcul);
</script>
@endpush