@extends('layouts.app')

@section('titre', 'Séquences pédagogiques')

@section('fil-ariane')
    <span>UVCI</span>
    <span class="separateur-ariane"><i class="bi bi-chevron-right"></i></span>
    <span>Gestion</span>
    <span class="separateur-ariane"><i class="bi bi-chevron-right"></i></span>
    <span>Séquences pédagogiques</span>
@endsection

@push('styles')
<style>
.modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.45);z-index:500;align-items:center;justify-content:center;padding:16px}
.modal-overlay.open{display:flex}
.modal-box{background:#fff;border-radius:14px;width:100%;max-width:540px;max-height:90vh;display:flex;flex-direction:column;box-shadow:0 24px 60px rgba(0,0,0,0.2);animation:min 0.22s ease}
@keyframes min{from{opacity:0;transform:translateY(-12px)}to{opacity:1;transform:translateY(0)}}
.modal-head{display:flex;align-items:center;justify-content:space-between;padding:20px 24px;border-bottom:1px solid var(--couleur-bordure);flex-shrink:0}
.modal-title{font-size:16px;font-weight:700;color:var(--couleur-texte-principal);display:flex;align-items:center;gap:8px}
.modal-title i{color:var(--couleur-principale);font-size:19px}
.modal-close{width:32px;height:32px;border:none;background:var(--couleur-fond);border-radius:7px;cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--couleur-texte-secondaire);transition:var(--transition)}
.modal-close:hover{background:var(--couleur-danger);color:#fff}
.modal-body{padding:24px;overflow-y:auto;flex:1}
.modal-foot{display:flex;align-items:center;justify-content:flex-end;gap:10px;padding:16px 24px;border-top:1px solid var(--couleur-bordure);background:var(--couleur-fond);border-radius:0 0 14px 14px;flex-shrink:0}
.champ-label{font-size:12.5px;font-weight:600;color:var(--couleur-texte-principal);display:block;margin-bottom:5px}
.champ-input{padding:9px 12px;border:1.5px solid var(--couleur-bordure);border-radius:var(--rayon-element);font-size:13.5px;font-family:var(--police);color:var(--couleur-texte-principal);background:#F8FAFF;outline:none;transition:border-color 0.2s;width:100%}
.champ-input:focus{border-color:var(--couleur-principale);background:#fff;box-shadow:0 0 0 3px rgba(67,97,238,0.1)}
select.champ-input{appearance:none;cursor:pointer;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%238B94B2' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 10px center;padding-right:30px}
.groupe-champ{margin-bottom:14px}
</style>
@endpush

@section('contenu')

<div class="en-tete-page">
    <div class="groupe-titre">
        <h1 class="titre-page">Séquences pédagogiques</h1>
        <p class="sous-titre-page">{{ $sequences->total() }} séquence(s) · 1 crédit = 40 séquences</p>
    </div>
    <div class="actions-page">
        <a href="{{ route('etats.index') }}" class="btn btn-contour">
            <i class="bi bi-download"></i>Exporter
        </a>
        <button class="btn btn-principal" onclick="ouvrirCreer()">
            <i class="bi bi-plus-lg"></i>Ajouter une séquence
        </button>
    </div>
</div>

{{-- Filtre par cours --}}
<div class="carte" style="margin-bottom:20px">
    <div class="corps-carte" style="padding:14px 22px">
        <form method="GET" style="display:flex;gap:12px;flex-wrap:wrap;align-items:center">
            <label style="font-size:13px;font-weight:600;color:var(--couleur-texte-secondaire);white-space:nowrap">
                Filtrer par cours :
            </label>
            <select name="cours_id" class="selecteur-filtre" style="flex:1;max-width:400px"
                    onchange="this.form.submit()">
                <option value="">— Tous les cours —</option>
                @foreach($cours as $c)
                    <option value="{{ $c->id }}"
                        {{ request('cours_id') == $c->id ? 'selected' : '' }}>
                        {{ $c->intitule }} ({{ $c->niveau }} — {{ $c->nombre_credits }} Cr
                        — {{ $c->anneeAcademique->libelle }})
                    </option>
                @endforeach
            </select>
        </form>
    </div>
</div>

{{-- Carte de progression du cours filtré --}}
@if($coursFiltreActuel)
    @php
        $totalPrevu   = $coursFiltreActuel->nombre_sequences;
        $totalCreees  = $sequences->total();
        $restantes    = max(0, $totalPrevu - $totalCreees);
        $pourcentage  = $totalPrevu > 0 ? min(100, round($totalCreees / $totalPrevu * 100)) : 0;
    @endphp
    <div class="carte" style="margin-bottom:20px">
        <div class="corps-carte">
            <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:16px">
                <div>
                    <div style="font-size:15px;font-weight:700;color:var(--couleur-texte-principal)">
                        {{ $coursFiltreActuel->intitule }}
                    </div>
                    <div style="font-size:12.5px;color:var(--couleur-texte-secondaire);margin-top:3px">
                        {{ $coursFiltreActuel->niveau }} · Semestre {{ $coursFiltreActuel->semestre }}
                        · {{ $coursFiltreActuel->filiere }}
                    </div>
                </div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px;margin-bottom:16px">
                <div style="text-align:center;background:var(--couleur-fond);border-radius:9px;padding:12px">
                    <div style="font-size:22px;font-weight:700;color:var(--couleur-principale)">{{ $totalPrevu }}</div>
                    <div style="font-size:11px;color:var(--couleur-texte-secondaire)">Séquences totales</div>
                </div>
                <div style="text-align:center;background:var(--couleur-fond);border-radius:9px;padding:12px">
                    <div style="font-size:22px;font-weight:700;color:var(--couleur-succes)">{{ $totalCreees }}</div>
                    <div style="font-size:11px;color:var(--couleur-texte-secondaire)">Créées</div>
                </div>
                <div style="text-align:center;background:var(--couleur-fond);border-radius:9px;padding:12px">
                    <div style="font-size:22px;font-weight:700;color:var(--couleur-avertissement)">{{ $restantes }}</div>
                    <div style="font-size:11px;color:var(--couleur-texte-secondaire)">Restantes</div>
                </div>
            </div>
            <div>
                <div class="info-progression">
                    <span>Progression de création</span>
                    <span style="font-weight:700">{{ $totalCreees }} / {{ $totalPrevu }} séquences</span>
                </div>
                <div class="barre-progression">
                    <div class="barre-progression-remplie" style="width:{{ $pourcentage }}%"></div>
                </div>
            </div>
        </div>
    </div>
@endif

{{-- Tableau --}}
<div class="carte">
    <div class="en-tete-carte">
        <div class="titre-carte"><i class="bi bi-list-ol"></i>Liste des séquences</div>
    </div>
    <div class="conteneur-tableau">
        <table class="tableau-donnees">
            <thead>
                <tr>
                    <th style="width:60px">N°</th>
                    <th>Titre</th>
                    <th class="colonne-masquable">Cours</th>
                    <th class="colonne-masquable">Ressources</th>
                    <th style="text-align:right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($sequences as $seq)
                <tr>
                    <td>
                        <span style="font-weight:700;color:var(--couleur-principale)">
                            {{ str_pad($seq->numero_ordre, 2, '0', STR_PAD_LEFT) }}
                        </span>
                    </td>
                    <td>
                        <div style="font-weight:600">{{ $seq->titre }}</div>
                        @if($seq->description)
                            <div class="detail-cellule">{{ Str::limit($seq->description, 60) }}</div>
                        @endif
                    </td>
                    <td class="texte-secondaire-tableau colonne-masquable">
                        {{ $seq->cours->intitule }}
                        <span class="badge badge-info" style="font-size:10px;margin-left:4px">
                            {{ $seq->cours->niveau }}
                        </span>
                    </td>
                    <td class="colonne-masquable">
                        @php $nbRessources = $seq->ressources->count() @endphp
                        @if($nbRessources > 0)
                            <span class="badge badge-succes">
                                <i class="bi bi-files"></i> {{ $nbRessources }}
                            </span>
                        @else
                            <span class="badge badge-neutre">Aucune</span>
                        @endif
                    </td>
                    <td>
                        <div class="actions-tableau" style="justify-content:flex-end">
                            <button class="btn-icone" title="Modifier"
                                onclick="ouvrirModifier(
                                    {{ $seq->id }},
                                    {{ $seq->cours_id }},
                                    '{{ addslashes($seq->titre) }}',
                                    {{ $seq->numero_ordre }},
                                    '{{ addslashes($seq->description ?? '') }}'
                                )">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form method="POST" action="{{ route('sequences.destroy', $seq) }}"
                                  onsubmit="return confirm('Supprimer cette séquence ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-icone danger">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5">
                    <div class="etat-vide">
                        <i class="bi bi-list-ol"></i>
                        <p>Aucune séquence trouvée</p>
                        <small>Sélectionnez un cours dans le filtre ci-dessus</small>
                        <button class="btn btn-principal btn-petit" style="margin-top:10px"
                                onclick="ouvrirCreer()">
                            <i class="bi bi-plus-lg"></i>Ajouter une séquence
                        </button>
                    </div>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="conteneur-pagination">
        <span>{{ $sequences->total() }} séquence(s)</span>
        {{ $sequences->withQueryString()->links() }}
    </div>
</div>

{{-- ══════════════ MODAL CRÉER ══════════════ --}}
<div class="modal-overlay" id="modal-creer">
    <div class="modal-box">
        <div class="modal-head">
            <div class="modal-title">
                <i class="bi bi-list-ol"></i>Ajouter une séquence
            </div>
            <button class="modal-close" onclick="fermerModal('modal-creer')">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="modal-body">
            <form method="POST" action="{{ route('sequences.store') }}" id="form-creer">
                @csrf
                <div class="groupe-champ">
                    <label class="champ-label">Cours *</label>
                    <select class="champ-input" name="cours_id" id="creer-cours" required>
                        <option value="">Sélectionner un cours…</option>
                        @foreach($cours as $c)
                            <option value="{{ $c->id }}"
                                {{ request('cours_id') == $c->id ? 'selected' : '' }}>
                                {{ $c->intitule }} ({{ $c->niveau }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="groupe-champ">
                    <label class="champ-label">Titre de la séquence *</label>
                    <input class="champ-input" type="text" name="titre"
                           placeholder="Ex. : Introduction au développement web" required>
                </div>
                <div class="groupe-champ">
                    <label class="champ-label">Numéro d'ordre *</label>
                    <input class="champ-input" type="number" name="numero_ordre"
                           min="1" placeholder="Ex. : 1" required>
                </div>
                <div class="groupe-champ">
                    <label class="champ-label">Description (optionnelle)</label>
                    <textarea class="champ-input" name="description" rows="3"
                              style="resize:vertical"
                              placeholder="Objectifs pédagogiques de la séquence…"></textarea>
                </div>
            </form>
        </div>
        <div class="modal-foot">
            <button class="btn btn-contour" onclick="fermerModal('modal-creer')">Annuler</button>
            <button class="btn btn-principal"
                    onclick="document.getElementById('form-creer').submit()">
                <i class="bi bi-check-lg"></i>Enregistrer
            </button>
        </div>
    </div>
</div>

{{-- ══════════════ MODAL MODIFIER ══════════════ --}}
<div class="modal-overlay" id="modal-modifier">
    <div class="modal-box">
        <div class="modal-head">
            <div class="modal-title">
                <i class="bi bi-pencil"></i>Modifier la séquence
            </div>
            <button class="modal-close" onclick="fermerModal('modal-modifier')">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="modal-body">
            <form method="POST" action="" id="form-modifier">
                @csrf @method('PUT')
                <div class="groupe-champ">
                    <label class="champ-label">Cours *</label>
                    <select class="champ-input" name="cours_id" id="mod-cours" required>
                        @foreach($cours as $c)
                            <option value="{{ $c->id }}">
                                {{ $c->intitule }} ({{ $c->niveau }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="groupe-champ">
                    <label class="champ-label">Titre *</label>
                    <input class="champ-input" type="text" name="titre"
                           id="mod-titre" required>
                </div>
                <div class="groupe-champ">
                    <label class="champ-label">Numéro d'ordre *</label>
                    <input class="champ-input" type="number" name="numero_ordre"
                           id="mod-ordre" min="1" required>
                </div>
                <div class="groupe-champ">
                    <label class="champ-label">Description</label>
                    <textarea class="champ-input" name="description" id="mod-desc"
                              rows="3" style="resize:vertical"></textarea>
                </div>
            </form>
        </div>
        <div class="modal-foot">
            <button class="btn btn-contour" onclick="fermerModal('modal-modifier')">Annuler</button>
            <button class="btn btn-principal"
                    onclick="document.getElementById('form-modifier').submit()">
                <i class="bi bi-check-lg"></i>Enregistrer
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function ouvrirCreer() {
    document.getElementById('form-creer').reset();
    document.getElementById('modal-creer').classList.add('open');
    document.body.style.overflow = 'hidden';
}

function ouvrirModifier(id, coursId, titre, ordre, desc) {
    var form = document.getElementById('form-modifier');
    form.action = '/sequences/' + id;
    document.getElementById('mod-cours').value = coursId;
    document.getElementById('mod-titre').value = titre;
    document.getElementById('mod-ordre').value = ordre;
    document.getElementById('mod-desc').value  = desc;
    document.getElementById('modal-modifier').classList.add('open');
    document.body.style.overflow = 'hidden';
}

function fermerModal(id) {
    document.getElementById(id).classList.remove('open');
    document.body.style.overflow = '';
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal-overlay.open').forEach(function(m) {
            m.classList.remove('open');
        });
        document.body.style.overflow = '';
    }
});

document.querySelectorAll('.modal-overlay').forEach(function(m) {
    m.addEventListener('click', function(e) {
        if (e.target === m) fermerModal(m.id);
    });
});
</script>
@endpush