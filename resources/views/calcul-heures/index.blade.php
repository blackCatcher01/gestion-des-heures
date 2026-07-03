@extends('layouts.app')

@section('titre', 'Calcul des heures')

@section('fil-ariane')
    <span>UVCI</span>
    <span class="separateur-ariane"><i class="bi bi-chevron-right"></i></span>
    <span>Suivi</span>
    <span class="separateur-ariane"><i class="bi bi-chevron-right"></i></span>
    <span>Calcul des heures</span>
@endsection

@section('contenu')

    <div class="en-tete-page">
        <div class="groupe-titre">
            <h1 class="titre-page">Calcul des heures</h1>
            <p class="sous-titre-page">
                Volumes horaires calculés par enseignant ·
                {{ $anneeActive ? 'Année '.$anneeActive->libelle : '' }}
            </p>
        </div>
        <div class="actions-page">
            <a href="{{ route('etats.excel') }}" class="btn btn-contour">
                <i class="bi bi-download"></i>Exporter tout
            </a>
        </div>
    </div>

    {{-- Simulateur --}}
    <div style="background:linear-gradient(135deg,rgba(67,97,238,0.06),rgba(114,9,183,0.06));
                border:1px solid rgba(67,97,238,0.15);border-radius:var(--rayon-carte);
                padding:22px;margin-bottom:22px">
        <div style="font-size:15px;font-weight:700;color:var(--couleur-texte-principal);
                    margin-bottom:16px;display:flex;align-items:center;gap:8px">
            <i class="bi bi-calculator" style="color:var(--couleur-principale)"></i>
            Simulateur — Volume = Séquences × Coefficient
        </div>
        <div style="display:grid;grid-template-columns:repeat(3,1fr) auto;gap:14px;align-items:end">
            <div>
                <label style="font-size:11.5px;font-weight:600;color:var(--couleur-texte-secondaire);
                               display:block;margin-bottom:5px">Crédits</label>
                <select id="sim-credits" onchange="simuler()"
                        style="width:100%;padding:8px 11px;border:1.5px solid var(--couleur-bordure);
                               border-radius:var(--rayon-element);font-size:13px;
                               font-family:var(--police);appearance:none;background:#fff;outline:none">
                    <option value="">—</option>
                    <option value="1">1 crédit (40 séq.)</option>
                    <option value="2">2 crédits (80 séq.)</option>
                    <option value="3">3 crédits (120 séq.)</option>
                    <option value="4">4 crédits (160 séq.)</option>
                </select>
            </div>
            <div>
                <label style="font-size:11.5px;font-weight:600;color:var(--couleur-texte-secondaire);
                               display:block;margin-bottom:5px">Type d'action</label>
                <select id="sim-type" onchange="simuler()"
                        style="width:100%;padding:8px 11px;border:1.5px solid var(--couleur-bordure);
                               border-radius:var(--rayon-element);font-size:13px;
                               font-family:var(--police);appearance:none;background:#fff;outline:none">
                    <option value="">—</option>
                    <option value="creation">Création</option>
                    <option value="mise_a_jour">Mise à jour</option>
                </select>
            </div>
            <div>
                <label style="font-size:11.5px;font-weight:600;color:var(--couleur-texte-secondaire);
                               display:block;margin-bottom:5px">Niveau</label>
                <select id="sim-niveau" onchange="simuler()"
                        style="width:100%;padding:8px 11px;border:1.5px solid var(--couleur-bordure);
                               border-radius:var(--rayon-element);font-size:13px;
                               font-family:var(--police);appearance:none;background:#fff;outline:none">
                    <option value="">—</option>
                    <option value="1">Niveau 1</option>
                    <option value="2">Niveau 2</option>
                    <option value="3">Niveau 3</option>
                </select>
            </div>
            <div style="background:#fff;border:1px solid rgba(67,97,238,0.3);border-radius:10px;
                        padding:8px 16px;text-align:center;white-space:nowrap">
                <div style="font-size:11px;color:var(--couleur-principale);font-weight:700;
                            text-transform:uppercase;letter-spacing:0.07em">Résultat</div>
                <div id="sim-resultat"
                     style="font-size:22px;font-weight:700;color:var(--couleur-principale)">
                    — h
                </div>
            </div>
        </div>
    </div>

    {{-- Tableau enseignants --}}
    <div style="display:flex;gap:10px;margin-bottom:16px;flex-wrap:wrap">
        <form method="GET" style="display:flex;gap:10px;flex-wrap:wrap">
            <select name="departement" class="selecteur-filtre" onchange="this.form.submit()">
                <option value="">Tous les départements</option>
                @foreach($departements as $dept)
                    <option value="{{ $dept->id }}"
                        {{ request('departement') == $dept->id ? 'selected' : '' }}>
                        {{ $dept->nom }}
                    </option>
                @endforeach
            </select>
            <select name="statut" class="selecteur-filtre" onchange="this.form.submit()">
                <option value="">Tous les statuts</option>
                <option value="permanent" {{ request('statut') === 'permanent' ? 'selected' : '' }}>
                    Permanent
                </option>
                <option value="vacataire" {{ request('statut') === 'vacataire' ? 'selected' : '' }}>
                    Vacataire
                </option>
            </select>
        </form>
    </div>

    @foreach($enseignants as $enseignant)
        @php $heures = $enseignant->heures_validees ?? 0; @endphp
        <div class="carte" style="margin-bottom:12px">
            <div style="display:flex;align-items:center;gap:14px;padding:18px 22px;
                        cursor:pointer;user-select:none"
                 onclick="toggleDetail('detail-{{ $enseignant->id }}', this)">
                <div class="avatar-tableau bleu" style="width:46px;height:46px;border-radius:12px;
                             display:flex;align-items:center;justify-content:center;
                             font-size:16px;font-weight:700;color:#fff;flex-shrink:0;
                             background:linear-gradient(135deg,#4361EE,#7209B7)">
                    {{ strtoupper(substr($enseignant->prenom,0,1).substr($enseignant->nom,0,1)) }}
                </div>
                <div style="flex:1">
                    <div style="font-size:15px;font-weight:700;color:var(--couleur-texte-principal)">
                        {{ $enseignant->nom_complet }}
                    </div>
                    <div style="font-size:12px;color:var(--couleur-texte-secondaire);margin-top:2px">
                        {{ ucfirst(str_replace('_','-',$enseignant->grade)) }} ·
                        {{ ucfirst($enseignant->statut) }} ·
                        {{ $enseignant->departement->nom }}
                    </div>
                </div>
                <div style="display:flex;gap:20px">
                    <div style="text-align:center">
                        <div style="font-size:20px;font-weight:700;color:var(--couleur-principale)">
                            {{ number_format($heures, 1, ',', '') }}
                        </div>
                        <div style="font-size:10px;color:var(--couleur-texte-secondaire)">
                            heures
                        </div>
                    </div>
                    <div style="text-align:center">
                        <div style="font-size:16px;font-weight:700;color:var(--couleur-succes)">
                            {{ number_format($heures * $enseignant->taux_horaire, 0, ',', ' ') }}
                        </div>
                        <div style="font-size:10px;color:var(--couleur-texte-secondaire)">FCFA</div>
                    </div>
                </div>
                <a href="{{ route('calcul-heures.detail', $enseignant) }}"
                   class="btn btn-contour btn-petit"
                   onclick="event.stopPropagation()">
                    Détail →
                </a>
                <i class="bi bi-chevron-right fleche-detail"
                   style="font-size:16px;color:var(--couleur-texte-secondaire);
                          transition:transform 0.2s;flex-shrink:0"></i>
            </div>

            {{-- Détail des activités (masqué par défaut) --}}
            <div id="detail-{{ $enseignant->id }}"
                 style="display:none;border-top:1px solid var(--couleur-bordure)">
                <div class="conteneur-tableau">
                    <table class="tableau-donnees">
                        <thead>
                            <tr>
                                <th>Cours</th>
                                <th>Action</th>
                                <th>Niveau</th>
                                <th>Séquences</th>
                                <th>Coefficient</th>
                                <th style="text-align:right">Volume (h)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($enseignant->activites()->with('cours')->where('statut','valide')->get() as $act)
                                @php
                                    $coef = $parametres[$act->type_action.'_'.$act->niveau_contenu]?->coefficient ?? 0;
                                @endphp
                                <tr>
                                    <td>
                                        <div style="font-weight:600">{{ $act->cours->intitule }}</div>
                                        <div class="detail-cellule">{{ $act->cours->niveau }}</div>
                                    </td>
                                    <td>
                                        @if($act->type_action === 'creation')
                                            <span class="badge badge-info" style="font-size:10px">Création</span>
                                        @else
                                            <span class="badge badge-violet" style="font-size:10px">Mise à jour</span>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge badge-neutre" style="font-size:10px">
                                            N{{ $act->niveau_contenu }}
                                        </span>
                                    </td>
                                    <td style="font-weight:600">{{ $act->cours->nombre_sequences }}</td>
                                    <td>
                                        <span style="font-size:12px;font-family:monospace;
                                                     background:var(--couleur-fond);padding:2px 7px;
                                                     border-radius:5px;color:var(--couleur-texte-secondaire)">
                                            ×{{ number_format($coef, 3, ',', '') }}
                                        </span>
                                    </td>
                                    <td style="text-align:right;font-weight:700;
                                               color:var(--couleur-principale)">
                                        {{ number_format($act->volume_horaire, 1, ',', '') }} h
                                    </td>
                                </tr>
                            @endforeach
                            <tr style="background:rgba(67,97,238,0.04);font-weight:700">
                                <td colspan="5"
                                    style="color:var(--couleur-texte-secondaire);padding:12px 16px">
                                    Total — {{ $enseignant->nom_complet }}
                                </td>
                                <td style="text-align:right;font-size:17px;
                                           color:var(--couleur-principale);padding:12px 16px;
                                           border-top:2px solid rgba(67,97,238,0.2)">
                                    {{ number_format($heures, 1, ',', '') }} h
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endforeach

@endsection

@push('scripts')
<script>
var COEFS = {
    'creation':    {'1': 0.40, '2': 0.75,  '3': 1.50},
    'mise_a_jour': {'1': 0.20, '2': 0.375, '3': 0.75}
};

function simuler() {
    var c = document.getElementById('sim-credits').value;
    var t = document.getElementById('sim-type').value;
    var n = document.getElementById('sim-niveau').value;
    var el = document.getElementById('sim-resultat');
    if (!c || !t || !n) { el.textContent = '— h'; return; }
    var total = parseInt(c) * 40 * COEFS[t][n];
    el.textContent = total.toFixed(1).replace('.', ',') + ' h';
}

function toggleDetail(id, btn) {
    var el = document.getElementById(id);
    var fleche = btn.querySelector('.fleche-detail');
    if (el.style.display === 'none') {
        el.style.display = 'block';
        if (fleche) fleche.style.transform = 'rotate(90deg)';
    } else {
        el.style.display = 'none';
        if (fleche) fleche.style.transform = '';
    }
}
</script>
@endpush