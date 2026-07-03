@extends('layouts.app')

@section('titre', 'Corriger une déclaration')

@section('fil-ariane')
    <span>UVCI</span>
    <span class="separateur-ariane"><i class="bi bi-chevron-right"></i></span>
    <a href="{{ route('espace.index') }}" style="color:var(--couleur-texte-secondaire)">Mon espace</a>
    <span class="separateur-ariane"><i class="bi bi-chevron-right"></i></span>
    <span>Corriger une déclaration</span>
@endsection

@section('contenu')

    <div class="en-tete-page">
        <div class="groupe-titre">
            <h1 class="titre-page">Corriger une déclaration</h1>
            <p class="sous-titre-page">
                Modifiez votre déclaration suite au rejet du secrétariat,
                puis soumettez-la à nouveau.
            </p>
        </div>
    </div>

    {{-- Motif de rejet --}}
    @if($activite->commentaire_rejet)
        <div style="background:#FDEEF0;border:1px solid #E63950;border-radius:10px;
                    padding:14px 16px;margin-bottom:20px;display:flex;gap:12px;
                    align-items:flex-start">
            <i class="bi bi-x-circle-fill"
               style="font-size:20px;color:#E63950;flex-shrink:0;margin-top:1px"></i>
            <div>
                <div style="font-size:13px;font-weight:700;color:#C42B3C;margin-bottom:4px">
                    Motif de rejet du secrétariat
                </div>
                <div style="font-size:13px;color:#C42B3C">
                    {{ $activite->commentaire_rejet }}
                </div>
            </div>
        </div>
    @endif

    <div style="display:grid;grid-template-columns:1fr 340px;gap:20px;align-items:start">

        <div class="carte">
            <div class="en-tete-carte">
                <div class="titre-carte">
                    <i class="bi bi-pencil"></i>Corriger la déclaration
                </div>
            </div>
            <div class="corps-carte">
                <form method="POST"
                      action="{{ route('espace.activites.update', $activite) }}"
                      id="form-correction">
                    @csrf
                    @method('PUT')

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
                        </label>
                        <select name="cours_id" id="select-cours" required
                                style="{{ $cs }};appearance:none;cursor:pointer"
                                onchange="majCalcul()">
                            <option value="">Sélectionner un cours…</option>
                            @foreach($cours as $c)
                                <option value="{{ $c->id }}"
                                        data-sequences="{{ $c->nombre_sequences }}"
                                    {{ (old('cours_id', $activite->cours_id) == $c->id) ? 'selected' : '' }}>
                                    {{ $c->intitule }}
                                    ({{ $c->niveau }} — {{ $c->nombre_credits }} crédit(s))
                                </option>
                            @endforeach
                        </select>
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
                                    {{ old('type_action', $activite->type_action) === 'creation' ? 'selected' : '' }}>
                                    Création de ressources
                                </option>
                                <option value="mise_a_jour"
                                    {{ old('type_action', $activite->type_action) === 'mise_a_jour' ? 'selected' : '' }}>
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
                                <option value="1"
                                    {{ old('niveau_contenu', $activite->niveau_contenu) == 1 ? 'selected' : '' }}>
                                    Niveau 1 — Contenus simples + quiz
                                </option>
                                <option value="2"
                                    {{ old('niveau_contenu', $activite->niveau_contenu) == 2 ? 'selected' : '' }}>
                                    Niveau 2 — Interactif + évaluations
                                </option>
                                <option value="3"
                                    {{ old('niveau_contenu', $activite->niveau_contenu) == 3 ? 'selected' : '' }}>
                                    Niveau 3 — Simulations / serious games
                                </option>
                            </select>
                        </div>
                    </div>

                    <div style="background:#E6F7FE;border:1px solid #4CC9F0;border-radius:9px;
                                padding:12px 14px;margin-bottom:20px;font-size:13px;color:#1B6FA8;
                                display:flex;gap:10px">
                        <i class="bi bi-info-circle" style="flex-shrink:0"></i>
                        <span>
                            Après correction, la déclaration repassera en statut
                            <strong>En attente</strong> pour une nouvelle vérification
                            par le secrétariat.
                        </span>
                    </div>

                    <div style="display:flex;gap:10px;justify-content:flex-end">
                        <a href="{{ route('espace.index') }}" class="btn btn-contour">
                            Annuler
                        </a>
                        <button type="submit" class="btn btn-principal">
                            <i class="bi bi-send"></i>Soumettre la correction
                        </button>
                    </div>

                </form>
            </div>
        </div>

        {{-- Calcul --}}
        <div class="carte">
            <div class="en-tete-carte">
                <div class="titre-carte">
                    <i class="bi bi-calculator"></i>Volume recalculé
                </div>
            </div>
            <div class="corps-carte">
                <div style="display:flex;flex-direction:column;gap:12px">
                    <div style="background:var(--couleur-fond);border-radius:9px;padding:12px;
                                display:flex;justify-content:space-between">
                        <span style="font-size:12px;color:var(--couleur-texte-secondaire)">Séquences</span>
                        <strong id="aff-seq">—</strong>
                    </div>
                    <div style="text-align:center;color:var(--couleur-texte-secondaire)">×</div>
                    <div style="background:var(--couleur-fond);border-radius:9px;padding:12px;
                                display:flex;justify-content:space-between">
                        <span style="font-size:12px;color:var(--couleur-texte-secondaire)">Coefficient</span>
                        <strong id="aff-coef">—</strong>
                    </div>
                    <div style="text-align:center;color:var(--couleur-texte-secondaire)">=</div>
                    <div style="background:linear-gradient(135deg,rgba(67,97,238,0.08),rgba(114,9,183,0.08));
                                border:1px solid rgba(67,97,238,0.2);border-radius:9px;padding:16px;
                                text-align:center">
                        <div style="font-size:11px;font-weight:700;color:var(--couleur-principale);
                                    text-transform:uppercase;letter-spacing:0.07em;margin-bottom:4px">
                            Nouveau volume
                        </div>
                        <div id="aff-volume"
                             style="font-size:32px;font-weight:700;color:var(--couleur-principale)">
                            — h
                        </div>
                    </div>
                    <div style="background:var(--couleur-fond);border-radius:9px;padding:12px">
                        <div style="font-size:11px;color:var(--couleur-texte-secondaire);margin-bottom:4px">
                            Volume précédent
                        </div>
                        <div style="font-size:16px;font-weight:600;
                                    color:var(--couleur-texte-secondaire);text-decoration:line-through">
                            {{ number_format($activite->volume_horaire, 1, ',', '') }} h
                        </div>
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