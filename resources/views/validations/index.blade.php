@extends('layouts.app')

@section('titre', 'Validations')

@section('fil-ariane')
    <span>UVCI</span>
    <span class="separateur-ariane"><i class="bi bi-chevron-right"></i></span>
    <span>Suivi</span>
    <span class="separateur-ariane"><i class="bi bi-chevron-right"></i></span>
    <span>Validations</span>
@endsection

@section('contenu')

    <div class="en-tete-page">
        <div class="groupe-titre">
            <h1 class="titre-page">Validations</h1>
            <p class="sous-titre-page">
                Vérifiez les activités sur Moodle avant de valider
            </p>
        </div>
        <div class="actions-page">
            @if($enAttente->count() > 0)
                <form method="POST" action="{{ route('validations.valider-tout') }}"
                      onsubmit="return confirm('Valider les {{ $enAttente->count() }} activités en attente ?')">
                    @csrf
                    <button type="submit" class="btn btn-succes">
                        <i class="bi bi-check2-all"></i>
                        Tout valider ({{ $enAttente->count() }})
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- Statistiques --}}
    <div class="grille-statistiques" style="margin-bottom:20px">
        <div class="carte-statistique accent-avertissement" style="padding:16px 18px">
            <div class="etiquette-stat">En attente</div>
            <div class="valeur-stat" style="font-size:26px">{{ $stats['en_attente'] }}</div>
            <div class="evolution-stat baisse"><i class="bi bi-exclamation-circle"></i>Action requise</div>
            <i class="bi bi-hourglass icone-fond-stat"></i>
        </div>
        <div class="carte-statistique accent-succes" style="padding:16px 18px">
            <div class="etiquette-stat">Validées aujourd'hui</div>
            <div class="valeur-stat" style="font-size:26px">{{ $stats['validees_aujourdhui'] }}</div>
            <div class="evolution-stat hausse"><i class="bi bi-arrow-up-short"></i>Ce jour</div>
            <i class="bi bi-check-circle icone-fond-stat"></i>
        </div>
        <div class="carte-statistique accent-danger" style="padding:16px 18px">
            <div class="etiquette-stat">Rejetées</div>
            <div class="valeur-stat" style="font-size:26px">{{ $stats['rejetees_total'] }}</div>
            <div class="evolution-stat">Au total</div>
            <i class="bi bi-x-circle icone-fond-stat"></i>
        </div>
        <div class="carte-statistique" style="padding:16px 18px">
            <div class="etiquette-stat">Heures en attente</div>
            <div class="valeur-stat" style="font-size:26px">{{ number_format($stats['heures_en_attente'], 0, ',', ' ') }}</div>
            <div class="evolution-stat">Volume total</div>
            <i class="bi bi-clock icone-fond-stat"></i>
        </div>
    </div>

    {{-- En attente --}}
    <div class="carte" style="margin-bottom:20px">
        <div class="en-tete-carte">
            <div class="titre-carte">
                <i class="bi bi-hourglass"></i>En attente de validation
            </div>
            @if($enAttente->count() > 0)
                <span class="badge badge-danger">{{ $enAttente->count() }}</span>
            @endif
        </div>

        @if($enAttente->isEmpty())
            <div class="etat-vide">
                <i class="bi bi-check-circle"></i>
                <p>Aucune activité en attente — tout est à jour</p>
            </div>
        @else
            <div class="conteneur-tableau">
                <table class="tableau-donnees">
                    <thead>
                        <tr>
                            <th>Enseignant</th>
                            <th>Cours</th>
                            <th class="colonne-masquable">Type</th>
                            <th class="colonne-masquable">Niveau</th>
                            <th>Heures</th>
                            <th class="colonne-masquable">Soumis le</th>
                            <th style="text-align:right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($enAttente as $activite)
                            <tr>
                                <td>
                                    <div class="cellule-personne">
                                        <div class="avatar-tableau bleu">
                                            {{ strtoupper(substr($activite->enseignant->prenom,0,1).substr($activite->enseignant->nom,0,1)) }}
                                        </div>
                                        <div>
                                            <div class="nom-cellule">
                                                {{ $activite->enseignant->nom_complet }}
                                            </div>
                                            <div class="detail-cellule">
                                                {{ $activite->enseignant->departement->nom }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div style="font-weight:600">{{ $activite->cours->intitule }}</div>
                                    <div class="detail-cellule">
                                        {{ $activite->cours->niveau }} —
                                        {{ $activite->cours->nombre_credits }} crédits
                                    </div>
                                </td>
                                <td class="colonne-masquable">
                                    @if($activite->type_action === 'creation')
                                        <span class="badge badge-info">Création</span>
                                    @else
                                        <span class="badge badge-violet">Mise à jour</span>
                                    @endif
                                </td>
                                <td class="colonne-masquable">
                                    <span class="badge badge-neutre">
                                        N{{ $activite->niveau_contenu }}
                                    </span>
                                </td>
                                <td>
                                    <strong style="color:var(--couleur-principale)">
                                        {{ number_format($activite->volume_horaire, 1, ',', '') }} h
                                    </strong>
                                </td>
                                <td class="texte-secondaire-tableau colonne-masquable">
                                    {{ $activite->created_at->format('d/m/Y') }}
                                </td>
                                <td>
                                    <div class="actions-tableau" style="justify-content:flex-end;gap:6px">
                                        <form method="POST"
                                              action="{{ route('validations.valider', $activite) }}">
                                            @csrf
                                            <button type="submit" class="btn btn-petit btn-succes">
                                                <i class="bi bi-check-lg"></i>Valider
                                            </button>
                                        </form>
                                        <button type="button" class="btn btn-petit btn-danger"
                                                onclick="ouvrirRejet({{ $activite->id }}, '{{ $activite->enseignant->nom_complet }} — {{ $activite->cours->intitule }}')">
                                            <i class="bi bi-x-lg"></i>Rejeter
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- Validées récentes --}}
    <div class="grille-deux-colonnes">
        <div class="carte">
            <div class="en-tete-carte">
                <div class="titre-carte"><i class="bi bi-check-lg"></i>Validées récemment</div>
            </div>
            <div class="conteneur-tableau">
                <table class="tableau-donnees">
                    <thead>
                        <tr>
                            <th>Enseignant</th>
                            <th>Cours</th>
                            <th>Heures</th>
                            <th class="colonne-masquable">Validé le</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($validees as $v)
                            <tr>
                                <td class="nom-cellule">{{ $v->enseignant->nom_complet }}</td>
                                <td class="texte-secondaire-tableau">{{ $v->cours->intitule }}</td>
                                <td><strong>{{ number_format($v->volume_horaire,1,',','') }} h</strong></td>
                                <td class="texte-secondaire-tableau colonne-masquable">
                                    {{ $v->date_validation?->format('d/m/Y') ?? '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4">
                                <div class="etat-vide" style="padding:20px">
                                    <i class="bi bi-inbox"></i>
                                    <p>Aucune activité validée</p>
                                </div>
                            </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="carte">
            <div class="en-tete-carte">
                <div class="titre-carte"><i class="bi bi-x-lg"></i>Rejetées récemment</div>
            </div>
            <div class="conteneur-tableau">
                <table class="tableau-donnees">
                    <thead>
                        <tr>
                            <th>Enseignant</th>
                            <th>Cours</th>
                            <th>Motif</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rejetees as $r)
                            <tr>
                                <td class="nom-cellule">{{ $r->enseignant->nom_complet }}</td>
                                <td class="texte-secondaire-tableau">{{ $r->cours->intitule }}</td>
                                <td style="font-size:12px;color:var(--couleur-texte-secondaire)">
                                    {{ Str::limit($r->commentaire_rejet, 60) }}
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3">
                                <div class="etat-vide" style="padding:20px">
                                    <i class="bi bi-inbox"></i>
                                    <p>Aucun rejet</p>
                                </div>
                            </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Modal rejet --}}
    <div id="modal-rejet" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.45);
                                  z-index:500;align-items:center;justify-content:center;padding:16px">
        <div style="background:#fff;border-radius:14px;width:100%;max-width:480px;
                    box-shadow:0 24px 60px rgba(0,0,0,0.2)">
            <div style="display:flex;align-items:center;justify-content:space-between;
                        padding:20px 24px;border-bottom:1px solid var(--couleur-bordure)">
                <div style="font-size:16px;font-weight:700;color:var(--couleur-danger);
                            display:flex;align-items:center;gap:8px">
                    <i class="bi bi-x-circle"></i>Rejeter l'activité
                </div>
                <button onclick="fermerRejet()"
                        style="width:32px;height:32px;border:none;background:var(--couleur-fond);
                               border-radius:7px;cursor:pointer;display:flex;align-items:center;
                               justify-content:center">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div style="padding:24px">
                <div style="background:#FDEEF0;border-radius:9px;padding:11px 13px;
                            margin-bottom:16px;font-size:13px;color:#C42B3C;
                            display:flex;gap:8px">
                    <i class="bi bi-exclamation-triangle" style="flex-shrink:0"></i>
                    <span id="label-activite-rejet"></span>
                </div>
                <form method="POST" id="form-rejet">
                    @csrf
                    <label style="font-size:12.5px;font-weight:600;display:block;margin-bottom:6px">
                        Motif de rejet *
                    </label>
                    <textarea name="commentaire_rejet" required rows="4"
                              placeholder="Expliquez précisément pourquoi cette déclaration est rejetée…"
                              style="width:100%;padding:9px 12px;border:1.5px solid var(--couleur-bordure);
                                     border-radius:var(--rayon-element);font-size:13.5px;
                                     font-family:var(--police);resize:vertical;outline:none">
                    </textarea>
                    <div style="display:flex;gap:10px;justify-content:flex-end;margin-top:16px">
                        <button type="button" onclick="fermerRejet()" class="btn btn-contour">
                            Annuler
                        </button>
                        <button type="submit" class="btn btn-danger">
                            <i class="bi bi-x-lg"></i>Confirmer le rejet
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
function ouvrirRejet(id, label) {
    document.getElementById('label-activite-rejet').textContent = label;
    document.getElementById('form-rejet').action = '/validations/' + id + '/rejeter';
    document.getElementById('modal-rejet').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function fermerRejet() {
    document.getElementById('modal-rejet').style.display = 'none';
    document.body.style.overflow = '';
}
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') fermerRejet();
});
</script>
@endpush