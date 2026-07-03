@extends('layouts.app')

@section('titre', 'Cours')

@section('fil-ariane')
    <span>UVCI</span>
    <span class="separateur-ariane"><i class="bi bi-chevron-right"></i></span>
    <span>Gestion</span>
    <span class="separateur-ariane"><i class="bi bi-chevron-right"></i></span>
    <span>Cours</span>
@endsection

@push('styles')
<style>
.modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.45);z-index:500;
               align-items:center;justify-content:center;padding:16px}
.modal-overlay.open{display:flex}
.modal-box{background:#fff;border-radius:14px;width:100%;max-width:600px;max-height:90vh;
           display:flex;flex-direction:column;box-shadow:0 24px 60px rgba(0,0,0,0.2);
           animation:modal-in 0.22s ease}
@keyframes modal-in{from{opacity:0;transform:translateY(-12px)}to{opacity:1;transform:translateY(0)}}
.modal-head{display:flex;align-items:center;justify-content:space-between;padding:20px 24px;
            border-bottom:1px solid var(--couleur-bordure);flex-shrink:0}
.modal-title{font-size:16px;font-weight:700;color:var(--couleur-texte-principal);
             display:flex;align-items:center;gap:8px}
.modal-title i{color:var(--couleur-principale);font-size:19px}
.modal-close{width:32px;height:32px;border:none;background:var(--couleur-fond);border-radius:7px;
             cursor:pointer;display:flex;align-items:center;justify-content:center;
             color:var(--couleur-texte-secondaire);transition:var(--transition)}
.modal-close:hover{background:var(--couleur-danger);color:#fff}
.modal-body{padding:24px;overflow-y:auto;flex:1}
.modal-foot{display:flex;align-items:center;justify-content:flex-end;gap:10px;
            padding:16px 24px;border-top:1px solid var(--couleur-bordure);
            background:var(--couleur-fond);border-radius:0 0 14px 14px;flex-shrink:0}
.grille-form{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.grille-form .full{grid-column:1/-1}
.champ-label{font-size:12.5px;font-weight:600;color:var(--couleur-texte-principal);
             display:block;margin-bottom:5px}
.champ-input{padding:9px 12px;border:1.5px solid var(--couleur-bordure);
             border-radius:var(--rayon-element);font-size:13.5px;font-family:var(--police);
             color:var(--couleur-texte-principal);background:#F8FAFF;outline:none;
             transition:border-color 0.2s;width:100%}
.champ-input:focus{border-color:var(--couleur-principale);background:#fff;
                   box-shadow:0 0 0 3px rgba(67,97,238,0.1)}
select.champ-input{appearance:none;cursor:pointer;
    background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%238B94B2' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
    background-repeat:no-repeat;background-position:right 10px center;padding-right:30px}
.seq-badge{font-size:12px;font-weight:700;color:var(--couleur-principale);
           background:rgba(67,97,238,0.08);padding:8px 12px;border-radius:7px;
           display:inline-block;margin-top:6px}
</style>
@endpush

@section('contenu')

<div class="en-tete-page">
    <div class="groupe-titre">
        <h1 class="titre-page">Cours</h1>
        <p class="sous-titre-page">{{ $cours->total() }} cours enregistrés</p>
    </div>
    <div class="actions-page">
        <a href="{{ route('etats.index') }}" class="btn btn-contour">
            <i class="bi bi-download"></i>Exporter
        </a>
        <button class="btn btn-principal" onclick="ouvrirCreer()">
            <i class="bi bi-plus-lg"></i>Ajouter un cours
        </button>
    </div>
</div>

<div class="grille-statistiques" style="grid-template-columns: repeat(4, 1fr); margin-bottom: 20px;">

    <div class="carte-statistique" style="padding: 16px 18px;">
        <div class="etiquette-stat">Total cours</div>
        <div class="valeur-stat" style="font-size:26px">{{ $stats['total'] }}</div>
        <div class="evolution-stat">Tous niveaux</div>
        <i class="bi bi-book icone-fond-stat"></i>
    </div>

    <div class="carte-statistique accent-info" style="padding: 16px 18px;">
        <div class="etiquette-stat">Niveau L1</div>
        <div class="valeur-stat" style="font-size:26px">{{ $stats['l1'] }}</div>
        <div class="evolution-stat">Première année</div>
        <i class="bi bi-layers icone-fond-stat"></i>
    </div>

    <div class="carte-statistique accent-succes" style="padding: 16px 18px;">
        <div class="etiquette-stat">Niveau L2</div>
        <div class="valeur-stat" style="font-size:26px">{{ $stats['l2'] }}</div>
        <div class="evolution-stat">Deuxième année</div>
        <i class="bi bi-lightning icone-fond-stat"></i>
    </div>

    <div class="carte-statistique accent-avertissement" style="padding: 16px 18px;">
        <div class="etiquette-stat">L3 et plus</div>
        <div class="valeur-stat" style="font-size:26px">{{ $stats['l3_plus'] }}</div>
        <div class="evolution-stat">Licence 3, Master</div>
        <i class="bi bi-stars icone-fond-stat"></i>
    </div>

</div>

<div class="carte">
    <div class="en-tete-carte">
        <div class="titre-carte"><i class="bi bi-table"></i>Liste des cours</div>
        <div class="barre-outils">
            <form method="GET" style="display:flex;gap:10px;flex-wrap:wrap">
                <div class="champ-recherche">
                    <i class="bi bi-search"></i>
                    <input type="text" name="recherche"
                           value="{{ request('recherche') }}" placeholder="Rechercher…">
                </div>
                <select name="niveau" class="selecteur-filtre" onchange="this.form.submit()">
                    <option value="">Tous niveaux</option>
                    @foreach(['L1','L2','L3','M1','M2'] as $n)
                        <option value="{{ $n }}" {{ request('niveau')===$n?'selected':'' }}>{{ $n }}</option>
                    @endforeach
                </select>
                <select name="annee" class="selecteur-filtre" onchange="this.form.submit()">
                    <option value="">Toutes années</option>
                    @foreach($annees as $a)
                        <option value="{{ $a->id }}" {{ request('annee')==$a->id?'selected':'' }}>
                            {{ $a->libelle }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    <div class="conteneur-tableau">
        <table class="tableau-donnees">
            <thead>
                <tr>
                    <th>Intitulé</th>
                    <th class="colonne-masquable">Filière</th>
                    <th>Niveau</th>
                    <th class="colonne-masquable">Sem.</th>
                    <th>Crédits</th>
                    <th>Séquences</th>
                    <th class="colonne-masquable">Année</th>
                    <th style="text-align:right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($cours as $c)
                <tr>
                    <td><div style="font-weight:600">{{ $c->intitule }}</div></td>
                    <td class="texte-secondaire-tableau colonne-masquable">{{ $c->filiere }}</td>
                    <td><span class="badge badge-info">{{ $c->niveau }}</span></td>
                    <td class="texte-secondaire-tableau colonne-masquable">S{{ $c->semestre }}</td>
                    <td><strong>{{ $c->nombre_credits }} Cr</strong></td>
                    <td><span style="font-weight:600;color:var(--couleur-principale)">{{ $c->nombre_sequences }}</span></td>
                    <td class="texte-secondaire-tableau colonne-masquable">{{ $c->anneeAcademique->libelle }}</td>
                    <td>
                        <div class="actions-tableau" style="justify-content:flex-end">
                            <button class="btn-icone" title="Modifier"
                                onclick="ouvrirModifier(
                                    {{ $c->id }},
                                    '{{ addslashes($c->intitule) }}',
                                    '{{ $c->filiere }}',
                                    '{{ $c->niveau }}',
                                    {{ $c->semestre }},
                                    {{ $c->nombre_credits }},
                                    {{ $c->annee_academique_id }}
                                )">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form method="POST" action="{{ route('cours.destroy', $c) }}"
                                  onsubmit="return confirm('Supprimer ce cours ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-icone danger">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8">
                    <div class="etat-vide">
                        <i class="bi bi-book"></i>
                        <p>Aucun cours trouvé</p>
                        <button class="btn btn-principal btn-petit" onclick="ouvrirCreer()">
                            <i class="bi bi-plus-lg"></i>Ajouter le premier cours
                        </button>
                    </div>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="conteneur-pagination">
        <span>{{ $cours->firstItem() ?? 0 }} à {{ $cours->lastItem() ?? 0 }} sur {{ $cours->total() }}</span>
        {{ $cours->withQueryString()->links() }}
    </div>
</div>

{{-- ═══════════════════════════════ MODAL CRÉER ═══════════════════════════════ --}}
<div class="modal-overlay" id="modal-creer">
    <div class="modal-box">
        <div class="modal-head">
            <div class="modal-title"><i class="bi bi-book-half"></i>Ajouter un cours</div>
            <button class="modal-close" onclick="fermerModal('modal-creer')">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="modal-body">
            <form method="POST" action="{{ route('cours.store') }}" id="form-creer">
                @csrf
                <div class="grille-form">

                    <div class="full">
                        <label class="champ-label">Intitulé du cours *</label>
                        <input class="champ-input" type="text" name="intitule"
                               placeholder="Ex. : Programmation Web" required>
                    </div>

                    <div>
                        <label class="champ-label">Filière *</label>
                        <input class="champ-input" type="text" name="filiere"
                               placeholder="Ex. : DAS, IEA…" required>
                    </div>

                    <div>
                        <label class="champ-label">Niveau *</label>
                        <select class="champ-input" name="niveau" required>
                            <option value="">Sélectionner…</option>
                            @foreach(['L1','L2','L3','M1','M2'] as $n)
                                <option value="{{ $n }}">{{ $n }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="champ-label">Semestre *</label>
                        <select class="champ-input" name="semestre" required>
                            <option value="">Sélectionner…</option>
                            <option value="1">Semestre 1</option>
                            <option value="2">Semestre 2</option>
                        </select>
                    </div>

                    <div>
                        <label class="champ-label">Crédits *</label>
                        <select class="champ-input" name="nombre_credits" required
                                onchange="majSeq(this,'aff-seq-creer')">
                            <option value="">Sélectionner…</option>
                            @foreach([1,2,3,4] as $cr)
                                <option value="{{ $cr }}">{{ $cr }} crédit(s)</option>
                            @endforeach
                        </select>
                        <div id="aff-seq-creer" class="seq-badge" style="display:none"></div>
                    </div>

                    <div class="full">
                        <label class="champ-label">Année académique *</label>
                        <select class="champ-input" name="annee_academique_id" required>
                            <option value="">Sélectionner…</option>
                            @foreach($annees as $a)
                                <option value="{{ $a->id }}" {{ $a->est_active?'selected':'' }}>
                                    {{ $a->libelle }}{{ $a->est_active?' (active)':'' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

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

{{-- ═══════════════════════════════ MODAL MODIFIER ═══════════════════════════════ --}}
<div class="modal-overlay" id="modal-modifier">
    <div class="modal-box">
        <div class="modal-head">
            <div class="modal-title"><i class="bi bi-pencil"></i>Modifier le cours</div>
            <button class="modal-close" onclick="fermerModal('modal-modifier')">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="modal-body">
            <form method="POST" action="" id="form-modifier">
                @csrf @method('PUT')
                <div class="grille-form">

                    <div class="full">
                        <label class="champ-label">Intitulé du cours *</label>
                        <input class="champ-input" type="text" name="intitule"
                               id="mod-intitule" required>
                    </div>

                    <div>
                        <label class="champ-label">Filière *</label>
                        <input class="champ-input" type="text" name="filiere"
                               id="mod-filiere" required>
                    </div>

                    <div>
                        <label class="champ-label">Niveau *</label>
                        <select class="champ-input" name="niveau" id="mod-niveau" required>
                            @foreach(['L1','L2','L3','M1','M2'] as $n)
                                <option value="{{ $n }}">{{ $n }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="champ-label">Semestre *</label>
                        <select class="champ-input" name="semestre" id="mod-semestre" required>
                            <option value="1">Semestre 1</option>
                            <option value="2">Semestre 2</option>
                        </select>
                    </div>

                    <div>
                        <label class="champ-label">Crédits *</label>
                        <select class="champ-input" name="nombre_credits" id="mod-credits" required
                                onchange="majSeq(this,'aff-seq-mod')">
                            @foreach([1,2,3,4] as $cr)
                                <option value="{{ $cr }}">{{ $cr }} crédit(s)</option>
                            @endforeach
                        </select>
                        <div id="aff-seq-mod" class="seq-badge"></div>
                    </div>

                    <div class="full">
                        <label class="champ-label">Année académique *</label>
                        <select class="champ-input" name="annee_academique_id"
                                id="mod-annee" required>
                            @foreach($annees as $a)
                                <option value="{{ $a->id }}">
                                    {{ $a->libelle }}{{ $a->est_active?' (active)':'' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                </div>
            </form>
        </div>
        <div class="modal-foot">
            <button class="btn btn-contour" onclick="fermerModal('modal-modifier')">Annuler</button>
            <button class="btn btn-principal"
                    onclick="document.getElementById('form-modifier').submit()">
                <i class="bi bi-check-lg"></i>Enregistrer les modifications
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
function ouvrirCreer() {
    document.getElementById('form-creer').reset();
    document.getElementById('aff-seq-creer').style.display = 'none';
    document.getElementById('modal-creer').classList.add('open');
    document.body.style.overflow = 'hidden';
}

function ouvrirModifier(id, intitule, filiere, niveau, semestre, credits, annee) {
    var form = document.getElementById('form-modifier');
    form.action = '/cours/' + id;
    document.getElementById('mod-intitule').value = intitule;
    document.getElementById('mod-filiere').value  = filiere;
    document.getElementById('mod-niveau').value   = niveau;
    document.getElementById('mod-semestre').value = semestre;
    document.getElementById('mod-credits').value  = credits;
    document.getElementById('mod-annee').value    = annee;
    // Afficher les séquences calculées
    var badge = document.getElementById('aff-seq-mod');
    badge.textContent = (credits * 40) + ' séquences';
    badge.style.display = 'inline-block';
    document.getElementById('modal-modifier').classList.add('open');
    document.body.style.overflow = 'hidden';
}

function fermerModal(id) {
    document.getElementById(id).classList.remove('open');
    document.body.style.overflow = '';
}

function majSeq(sel, affId) {
    var badge = document.getElementById(affId);
    if (!sel.value) { badge.style.display = 'none'; return; }
    badge.textContent = (parseInt(sel.value) * 40) + ' séquences';
    badge.style.display = 'inline-block';
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