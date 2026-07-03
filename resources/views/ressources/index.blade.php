@extends('layouts.app')

@section('titre', 'Ressources pédagogiques')

@section('fil-ariane')
    <span>UVCI</span>
    <span class="separateur-ariane"><i class="bi bi-chevron-right"></i></span>
    <span>Gestion</span>
    <span class="separateur-ariane"><i class="bi bi-chevron-right"></i></span>
    <span>Ressources pédagogiques</span>
@endsection

@push('styles')
<style>
.modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.45);z-index:500;align-items:center;justify-content:center;padding:16px}
.modal-overlay.open{display:flex}
.modal-box{background:#fff;border-radius:14px;width:100%;max-width:560px;max-height:90vh;display:flex;flex-direction:column;box-shadow:0 24px 60px rgba(0,0,0,0.2);animation:min 0.22s ease}
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
.icone-type{width:36px;height:36px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0}
.t-pdf{background:#FDEEF0;color:#E63950}
.t-video{background:#EAF0FE;color:#4361EE}
.t-quiz{background:#FEF5E0;color:#D4910A}
.t-interactif{background:#E7FBF3;color:#05C48A}
.t-evaluation{background:#F2EAFE;color:#7209B7}
</style>
@endpush

@section('contenu')

<div class="en-tete-page">
    <div class="groupe-titre">
        <h1 class="titre-page">Ressources pédagogiques</h1>
        <p class="sous-titre-page">
            Métadonnées des contenus — les fichiers restent hébergés sur Moodle
        </p>
    </div>
    <div class="actions-page">
        <a href="{{ route('etats.index') }}" class="btn btn-contour">
            <i class="bi bi-download"></i>Exporter
        </a>
        <button class="btn btn-principal" onclick="ouvrirCreer()">
            <i class="bi bi-plus-lg"></i>Ajouter une ressource
        </button>
    </div>
</div>

{{-- Filtres --}}
<div class="carte" style="margin-bottom:20px">
    <div class="corps-carte" style="padding:14px 22px">
        <form method="GET" style="display:flex;gap:10px;flex-wrap:wrap;align-items:center">
            <select name="cours_id" class="selecteur-filtre" style="flex:1;max-width:340px"
                    onchange="this.form.submit()">
                <option value="">— Tous les cours —</option>
                @foreach($cours as $c)
                    <option value="{{ $c->id }}"
                        {{ request('cours_id') == $c->id ? 'selected' : '' }}>
                        {{ $c->intitule }} ({{ $c->niveau }})
                    </option>
                @endforeach
            </select>
            @if($sequences->count())
                <select name="sequence_id" class="selecteur-filtre"
                        onchange="this.form.submit()">
                    <option value="">— Toutes les séquences —</option>
                    @foreach($sequences as $s)
                        <option value="{{ $s->id }}"
                            {{ request('sequence_id') == $s->id ? 'selected' : '' }}>
                            Séq. {{ str_pad($s->numero_ordre,2,'0',STR_PAD_LEFT) }}
                            — {{ Str::limit($s->titre,40) }}
                        </option>
                    @endforeach
                </select>
            @endif
            <select name="type" class="selecteur-filtre" onchange="this.form.submit()">
                <option value="">Tous les types</option>
                @foreach(['pdf'=>'Document PDF','video'=>'Vidéo','quiz'=>'Quiz',
                          'interactif'=>'Interactif','evaluation'=>'Évaluation'] as $val=>$lab)
                    <option value="{{ $val }}" {{ request('type')===$val?'selected':'' }}>
                        {{ $lab }}
                    </option>
                @endforeach
            </select>
        </form>
    </div>
</div>

{{-- Stats rapides --}}
<div class="grille-statistiques" style="margin-bottom:20px">
    <div class="carte-statistique" style="padding:16px 18px">
        <div class="etiquette-stat">Total ressources</div>
        <div class="valeur-stat" style="font-size:26px">{{ $ressources->total() }}</div>
        <div class="evolution-stat">Toutes séquences</div>
        <i class="bi bi-files icone-fond-stat"></i>
    </div>
    <div class="carte-statistique accent-danger" style="padding:16px 18px">
        <div class="etiquette-stat">Documents PDF</div>
        <div class="valeur-stat" style="font-size:26px">{{ $statsRessources['pdf'] }}</div>
        <div class="evolution-stat">{{ $statsRessources['pourcentage_pdf'] }}%</div>
        <i class="bi bi-file-earmark-pdf icone-fond-stat"></i>
    </div>
    <div class="carte-statistique accent-info" style="padding:16px 18px">
        <div class="etiquette-stat">Vidéos</div>
        <div class="valeur-stat" style="font-size:26px">{{ $statsRessources['video'] }}</div>
        <div class="evolution-stat">{{ $statsRessources['pourcentage_video'] }}%</div>
        <i class="bi bi-camera-video icone-fond-stat"></i>
    </div>
    <div class="carte-statistique accent-avertissement" style="padding:16px 18px">
        <div class="etiquette-stat">Quiz & évaluations</div>
        <div class="valeur-stat" style="font-size:26px">{{ $statsRessources['quiz_eval'] }}</div>
        <div class="evolution-stat">{{ $statsRessources['pourcentage_quiz_eval'] }}%</div>
        <i class="bi bi-question-circle icone-fond-stat"></i>
    </div>
</div>

{{-- Barre de bascule --}}
<div class="carte" style="margin-bottom:20px">
    <div class="corps-carte" style="padding:14px 22px">
        <div class="barre-outils">
            <div style="flex:1"></div>
            <div style="display:flex;gap:5px">
                <button class="btn-icone actif" id="btn-vue-grille" onclick="basculerVueRessources('grille')" title="Vue grille">
                    <i class="bi bi-grid-3x3-gap"></i>
                </button>
                <button class="btn-icone" id="btn-vue-liste" onclick="basculerVueRessources('liste')" title="Vue liste">
                    <i class="bi bi-list-ul"></i>
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Vue grille --}}
<div id="vue-grille-ressources" class="carte">
    <div class="grille-ressources">
        @forelse($ressources as $res)
            @php
                $icones = ['pdf'=>'bi-file-earmark-pdf','video'=>'bi-camera-video','quiz'=>'bi-question-circle',
                           'interactif'=>'bi-lightning','evaluation'=>'bi-clipboard-check'];
                $badges = ['pdf'=>'badge-danger','video'=>'badge-info','quiz'=>'badge-avertissement',
                           'interactif'=>'badge-succes','evaluation'=>'badge-violet'];
                $labels = ['pdf'=>'PDF','video'=>'Vidéo','quiz'=>'Quiz',
                           'interactif'=>'Interactif','evaluation'=>'Évaluation'];
            @endphp
            <div class="carte-ressource">
                <div class="entete-ressource">
                    <div class="icone-type t-{{ $res->type }}"><i class="bi {{ $icones[$res->type] }}"></i></div>
                    <div class="info-ressource">
                        <div class="titre-ressource">{{ $res->titre }}</div>
                        <div class="sous-titre-ressource">
                            Séquence {{ str_pad($res->sequence->numero_ordre,2,'0',STR_PAD_LEFT) }}
                            — {{ Str::limit($res->sequence->cours->intitule, 25) }}
                        </div>
                    </div>
                </div>
                <div class="meta-ressource">
                    <span class="badge {{ $badges[$res->type] }}">{{ $labels[$res->type] }}</span>
                    @if($res->url_moodle)
                        <span class="badge badge-neutre">Moodle ✓</span>
                    @endif
                    @if($res->activite)
                        <span class="badge badge-violet" title="Produite via cette activité">
                            <i class="bi bi-link-45deg"></i> {{ $res->activite->enseignant->nom_complet }}
                        </span>
                    @endif
                </div>
            </div>
        @empty
            <div class="etat-vide">
                <i class="bi bi-file-earmark-richtext"></i>
                <p>Aucune ressource trouvée</p>
                <button class="btn btn-principal btn-petit" style="margin-top:10px" onclick="ouvrirCreer()">
                    <i class="bi bi-plus-lg"></i>Ajouter la première ressource
                </button>
            </div>
        @endforelse
    </div>
    <div class="conteneur-pagination">
        <span>{{ $ressources->total() }} ressource(s)</span>
        {{ $ressources->withQueryString()->links() }}
    </div>
</div>

{{-- Vue liste (cachée par défaut) --}}
<div id="vue-liste-ressources" class="carte" style="display:none">
    <div class="en-tete-carte">
        <div class="titre-carte"><i class="bi bi-table"></i>Liste des ressources</div>
    </div>
    <div class="conteneur-tableau">
        <table class="tableau-donnees">
            <thead>
                <tr>
                    <th>Ressource</th>
                    <th>Type</th>
                    <th class="colonne-masquable">Séquence</th>
                    <th class="colonne-masquable">Cours</th>
                    <th class="colonne-masquable">Activité liée</th>
                    <th>URL Moodle</th>
                    <th style="text-align:right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($ressources as $res)
                <tr>
                    <td>
                        <div style="display:flex;align-items:center;gap:10px">
                            <div class="icone-type t-{{ $res->type }}">
                                @switch($res->type)
                                    @case('pdf')<i class="bi bi-file-earmark-pdf"></i>@break
                                    @case('video')<i class="bi bi-camera-video"></i>@break
                                    @case('quiz')<i class="bi bi-question-circle"></i>@break
                                    @case('interactif')<i class="bi bi-lightning"></i>@break
                                    @case('evaluation')<i class="bi bi-clipboard-check"></i>@break
                                @endswitch
                            </div>
                            <div>
                                <div style="font-weight:600">{{ $res->titre }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        @php
                            $badges = ['pdf'=>'badge-danger','video'=>'badge-info',
                                       'quiz'=>'badge-avertissement','interactif'=>'badge-succes',
                                       'evaluation'=>'badge-violet'];
                            $labels = ['pdf'=>'PDF','video'=>'Vidéo','quiz'=>'Quiz',
                                       'interactif'=>'Interactif','evaluation'=>'Évaluation'];
                        @endphp
                        <span class="badge {{ $badges[$res->type] }}">
                            {{ $labels[$res->type] }}
                        </span>
                    </td>
                    <td class="texte-secondaire-tableau colonne-masquable">
                        Séq. {{ str_pad($res->sequence->numero_ordre,2,'0',STR_PAD_LEFT) }}
                        — {{ Str::limit($res->sequence->titre, 35) }}
                    </td>
                    <td class="texte-secondaire-tableau colonne-masquable">
                        {{ Str::limit($res->sequence->cours->intitule, 30) }}
                    </td>
                    <td class="texte-secondaire-tableau colonne-masquable">
                        @if($res->activite)
                            <span class="badge badge-violet" style="font-size:10px">
                                <i class="bi bi-link-45deg"></i>
                                {{ $res->activite->enseignant->nom_complet }}
                            </span>
                        @else
                            —
                        @endif
                    </td>
                    <td>
                        @if($res->url_moodle)
                            <a href="{{ $res->url_moodle }}" target="_blank"
                               style="font-size:12px;color:var(--couleur-principale)">
                                <i class="bi bi-box-arrow-up-right"></i> Ouvrir
                            </a>
                        @else
                            <span class="texte-secondaire-tableau">—</span>
                        @endif
                    </td>
                    <td>
                        <div class="actions-tableau" style="justify-content:flex-end">
                            <button class="btn-icone" title="Modifier"
                                onclick="ouvrirModifier(
                                    {{ $res->id }},
                                    {{ $res->sequence_id }},
                                    '{{ addslashes($res->titre) }}',
                                    '{{ $res->type }}',
                                    '{{ addslashes($res->url_moodle ?? '') }}',
                                    {{ $res->activite_id ?? 'null' }}
                                )">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form method="POST" action="{{ route('ressources.destroy', $res) }}"
                                  onsubmit="return confirm('Supprimer cette ressource ?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-icone danger">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7">
                    <div class="etat-vide">
                        <i class="bi bi-file-earmark-richtext"></i>
                        <p>Aucune ressource trouvée</p>
                        <button class="btn btn-principal btn-petit" style="margin-top:10px"
                                onclick="ouvrirCreer()">
                            <i class="bi bi-plus-lg"></i>Ajouter la première ressource
                        </button>
                    </div>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="conteneur-pagination">
        <span>{{ $ressources->total() }} ressource(s)</span>
        {{ $ressources->withQueryString()->links() }}
    </div>
</div>

{{-- ══════════════ MODAL CRÉER ══════════════ --}}
<div class="modal-overlay" id="modal-creer">
    <div class="modal-box">
        <div class="modal-head">
            <div class="modal-title">
                <i class="bi bi-file-earmark-plus"></i>Ajouter une ressource
            </div>
            <button class="modal-close" onclick="fermerModal('modal-creer')">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="modal-body">
            <div style="background:#E6F7FE;border:1px solid #4CC9F0;border-radius:9px;
                        padding:10px 14px;margin-bottom:16px;font-size:12.5px;color:#1B6FA8;
                        display:flex;gap:8px">
                <i class="bi bi-info-circle" style="flex-shrink:0"></i>
                L'application enregistre uniquement les métadonnées.
                Le fichier doit rester hébergé sur Moodle.
            </div>
            <form method="POST" action="{{ route('ressources.store') }}" id="form-creer">
                @csrf
                <div class="groupe-champ">
                    <label class="champ-label">Séquence associée *</label>
                    <select class="champ-input" name="sequence_id" id="creer-seq" required>
                        <option value="">Sélectionner une séquence…</option>
                        @foreach($cours as $c)
                            <optgroup label="{{ $c->intitule }} ({{ $c->niveau }})">
                                @foreach($c->sequences as $s)
                                    <option value="{{ $s->id }}"
                                        {{ request('sequence_id') == $s->id ? 'selected' : '' }}>
                                        Séq. {{ str_pad($s->numero_ordre,2,'0',STR_PAD_LEFT) }}
                                        — {{ $s->titre }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>
                <div class="groupe-champ">
                    <label class="champ-label">Titre de la ressource *</label>
                    <input class="champ-input" type="text" name="titre"
                           placeholder="Ex. : Support de cours — HTML5" required>
                </div>
                <div class="groupe-champ">
                    <label class="champ-label">Type de ressource *</label>
                    <select class="champ-input" name="type" required>
                        <option value="">Sélectionner…</option>
                        <option value="pdf">Document PDF</option>
                        <option value="video">Vidéo pédagogique</option>
                        <option value="quiz">Quiz</option>
                        <option value="interactif">Activité interactive</option>
                        <option value="evaluation">Évaluation</option>
                    </select>
                </div>
                <div class="groupe-champ">
                    <label class="champ-label">URL Moodle</label>
                    <input class="champ-input" type="url" name="url_moodle"
                           placeholder="https://moodle.uvci.edu.ci/…">
                </div>
                <div class="groupe-champ">
                    <label class="champ-label">Activité liée (optionnel)</label>
                    <select class="champ-input" name="activite_id" id="creer-activite">
                        <option value="">— Aucune activité —</option>
                        @foreach($cours as $c)
                            @php $acts = $activites->where('cours_id', $c->id) @endphp
                            @if($acts->isNotEmpty())
                                <optgroup label="{{ $c->intitule }} ({{ $c->niveau }})">
                                    @foreach($acts as $a)
                                        <option value="{{ $a->id }}">
                                            {{ $a->enseignant->nom_complet }} —
                                            {{ $a->type_action === 'creation' ? 'Création' : 'Mise à jour' }}
                                            (Niveau {{ $a->niveau_contenu }})
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endif
                        @endforeach
                    </select>
                    <div style="font-size:11px;color:var(--couleur-texte-secondaire);margin-top:4px">
                        Rattache cette ressource à la déclaration d'activité qui l'a produite.
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

{{-- ══════════════ MODAL MODIFIER ══════════════ --}}
<div class="modal-overlay" id="modal-modifier">
    <div class="modal-box">
        <div class="modal-head">
            <div class="modal-title">
                <i class="bi bi-pencil"></i>Modifier la ressource
            </div>
            <button class="modal-close" onclick="fermerModal('modal-modifier')">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
        <div class="modal-body">
            <form method="POST" action="" id="form-modifier">
                @csrf @method('PUT')
                <div class="groupe-champ">
                    <label class="champ-label">Séquence associée *</label>
                    <select class="champ-input" name="sequence_id" id="mod-seq" required>
                        @foreach($cours as $c)
                            <optgroup label="{{ $c->intitule }} ({{ $c->niveau }})">
                                @foreach($c->sequences as $s)
                                    <option value="{{ $s->id }}">
                                        Séq. {{ str_pad($s->numero_ordre,2,'0',STR_PAD_LEFT) }}
                                        — {{ $s->titre }}
                                    </option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>
                <div class="groupe-champ">
                    <label class="champ-label">Titre *</label>
                    <input class="champ-input" type="text" name="titre" id="mod-titre" required>
                </div>
                <div class="groupe-champ">
                    <label class="champ-label">Type *</label>
                    <select class="champ-input" name="type" id="mod-type" required>
                        <option value="pdf">Document PDF</option>
                        <option value="video">Vidéo pédagogique</option>
                        <option value="quiz">Quiz</option>
                        <option value="interactif">Activité interactive</option>
                        <option value="evaluation">Évaluation</option>
                    </select>
                </div>
                <div class="groupe-champ">
                    <label class="champ-label">URL Moodle</label>
                    <input class="champ-input" type="url" name="url_moodle"
                           id="mod-url" placeholder="https://moodle.uvci.edu.ci/…">
                </div>
                <div class="groupe-champ">
                    <label class="champ-label">Activité liée (optionnel)</label>
                    <select class="champ-input" name="activite_id" id="mod-activite">
                        <option value="">— Aucune activité —</option>
                        @foreach($cours as $c)
                            @php $acts = $activites->where('cours_id', $c->id) @endphp
                            @if($acts->isNotEmpty())
                                <optgroup label="{{ $c->intitule }} ({{ $c->niveau }})">
                                    @foreach($acts as $a)
                                        <option value="{{ $a->id }}">
                                            {{ $a->enseignant->nom_complet }} —
                                            {{ $a->type_action === 'creation' ? 'Création' : 'Mise à jour' }}
                                            (Niveau {{ $a->niveau_contenu }})
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endif
                        @endforeach
                    </select>
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
function basculerVueRessources(vue) {
    var grille = document.getElementById('vue-grille-ressources');
    var liste = document.getElementById('vue-liste-ressources');
    var btnGrille = document.getElementById('btn-vue-grille');
    var btnListe = document.getElementById('btn-vue-liste');
    if (vue === 'grille') {
        grille.style.display = '';
        liste.style.display = 'none';
        btnGrille.classList.add('actif');
        btnListe.classList.remove('actif');
    } else {
        grille.style.display = 'none';
        liste.style.display = '';
        btnListe.classList.add('actif');
        btnGrille.classList.remove('actif');
    }
}

function ouvrirCreer() {
    document.getElementById('form-creer').reset();
    document.getElementById('modal-creer').classList.add('open');
    document.body.style.overflow = 'hidden';
}

function ouvrirModifier(id, seqId, titre, type, url, activiteId) {
    var form = document.getElementById('form-modifier');
    form.action = '/ressources/' + id;
    document.getElementById('mod-seq').value      = seqId;
    document.getElementById('mod-titre').value    = titre;
    document.getElementById('mod-type').value     = type;
    document.getElementById('mod-url').value      = url;
    document.getElementById('mod-activite').value = activiteId || '';
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