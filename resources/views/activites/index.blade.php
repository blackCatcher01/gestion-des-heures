@extends('layouts.app')

@section('titre', 'Activités pédagogiques')

@section('fil-ariane')
    <span>UVCI</span>
    <span class="separateur-ariane"><i class="bi bi-chevron-right"></i></span>
    <span>Suivi</span>
    <span class="separateur-ariane"><i class="bi bi-chevron-right"></i></span>
    <span>Activités pédagogiques</span>
@endsection

@section('contenu')

    <div class="en-tete-page">
        <div class="groupe-titre">
            <h1 class="titre-page">Activités pédagogiques</h1>
            <p class="sous-titre-page">
                Déclarations des enseignants — calcul automatique des volumes horaires
            </p>
        </div>
        <div class="actions-page">
            <a href="{{ route('etats.index') }}" class="btn btn-contour">
                <i class="bi bi-download"></i>Exporter
            </a>
            <a href="{{ route('activites.create') }}" class="btn btn-principal">
                <i class="bi bi-plus-lg"></i>Déclarer une activité
            </a>
        </div>
    </div>

    {{-- Statistiques rapides --}}
    <div class="grille-statistiques" style="margin-bottom:20px">
        <div class="carte-statistique" style="padding:16px 18px">
            <div class="etiquette-stat">Total</div>
            <div class="valeur-stat" style="font-size:26px">{{ $stats['total'] }}</div>
            <div class="evolution-stat">Toutes déclarations</div>
            <i class="bi bi-activity icone-fond-stat"></i>
        </div>
        <div class="carte-statistique accent-avertissement" style="padding:16px 18px">
            <div class="etiquette-stat">En attente</div>
            <div class="valeur-stat" style="font-size:26px">{{ $stats['en_attente'] }}</div>
            <div class="evolution-stat baisse"><i class="bi bi-exclamation-circle"></i> À valider</div>
            <i class="bi bi-hourglass icone-fond-stat"></i>
        </div>
        <div class="carte-statistique accent-succes" style="padding:16px 18px">
            <div class="etiquette-stat">Validées</div>
            <div class="valeur-stat" style="font-size:26px">{{ $stats['valide'] }}</div>
            <div class="evolution-stat hausse"><i class="bi bi-check-circle"></i></div>
            <i class="bi bi-check-circle icone-fond-stat"></i>
        </div>
        <div class="carte-statistique accent-danger" style="padding:16px 18px">
            <div class="etiquette-stat">Rejetées</div>
            <div class="valeur-stat" style="font-size:26px">{{ $stats['rejete'] }}</div>
            <div class="evolution-stat">À corriger</div>
            <i class="bi bi-x-circle icone-fond-stat"></i>
        </div>
    </div>

    <div class="carte">
        <div class="en-tete-carte">
            <div class="titre-carte"><i class="bi bi-table"></i>Liste des activités</div>
            <div class="barre-outils">
                <form method="GET" style="display:flex;gap:10px;flex-wrap:wrap">
                    <select name="statut" class="selecteur-filtre" onchange="this.form.submit()">
                        <option value="">Tous les statuts</option>
                        <option value="en_attente" {{ request('statut') === 'en_attente' ? 'selected' : '' }}>
                            En attente
                        </option>
                        <option value="valide" {{ request('statut') === 'valide' ? 'selected' : '' }}>
                            Validées
                        </option>
                        <option value="rejete" {{ request('statut') === 'rejete' ? 'selected' : '' }}>
                            Rejetées
                        </option>
                    </select>
                    <select name="type_action" class="selecteur-filtre" onchange="this.form.submit()">
                        <option value="">Tous types</option>
                        <option value="creation" {{ request('type_action') === 'creation' ? 'selected' : '' }}>
                            Création
                        </option>
                        <option value="mise_a_jour" {{ request('type_action') === 'mise_a_jour' ? 'selected' : '' }}>
                            Mise à jour
                        </option>
                    </select>
                </form>
            </div>
        </div>

        <div class="conteneur-tableau">
            <table class="tableau-donnees">
                <thead>
                    <tr>
                        <th>Enseignant</th>
                        <th>Cours</th>
                        <th class="colonne-masquable">Type d'action</th>
                        <th class="colonne-masquable">Niv. contenu</th>
                        <th>Vol. horaire</th>
                        <th>Statut</th>
                        <th class="colonne-masquable">Date</th>
                        <th style="text-align:right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($activites as $activite)
                        <tr>
                            <td>
                                <div class="cellule-personne">
                                    <div class="avatar-tableau bleu">
                                        {{ strtoupper(substr($activite->enseignant->prenom,0,1).substr($activite->enseignant->nom,0,1)) }}
                                    </div>
                                    <div class="nom-cellule">
                                        {{ $activite->enseignant->nom_complet }}
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div style="font-weight:600">{{ $activite->cours->intitule }}</div>
                                <div class="detail-cellule">
                                    {{ $activite->cours->niveau }} — {{ $activite->cours->nombre_credits }} crédits
                                </div>
                            </td>
                            <td class="colonne-masquable">
                                @if($activite->type_action === 'creation')
                                    <span class="badge badge-info">
                                        <i class="bi bi-plus-circle"></i> Création
                                    </span>
                                @else
                                    <span class="badge badge-violet">
                                        <i class="bi bi-arrow-repeat"></i> Mise à jour
                                    </span>
                                @endif
                            </td>
                            <td class="colonne-masquable">
                                <span class="badge badge-neutre">
                                    Niveau {{ $activite->niveau_contenu }}
                                </span>
                            </td>
                            <td>
                                <strong style="color:var(--couleur-principale)">
                                    {{ number_format($activite->volume_horaire, 1, ',', '') }} h
                                </strong>
                            </td>
                            <td>
                                @switch($activite->statut)
                                    @case('en_attente')
                                        <span class="badge badge-avertissement">
                                            <i class="bi bi-clock"></i> En attente
                                        </span>
                                        @break
                                    @case('valide')
                                        <span class="badge badge-succes">
                                            <i class="bi bi-check-lg"></i> Validée
                                        </span>
                                        @break
                                    @case('rejete')
                                        <span class="badge badge-danger">
                                            <i class="bi bi-x-lg"></i> Rejetée
                                        </span>
                                        @break
                                    @case('verrouille')
                                        <span class="badge badge-neutre">
                                            <i class="bi bi-lock"></i> Verrouillée
                                        </span>
                                        @break
                                @endswitch
                            </td>
                            <td class="texte-secondaire-tableau colonne-masquable">
                                {{ $activite->created_at->format('d/m/Y') }}
                            </td>
                            <td>
                                <div class="actions-tableau" style="justify-content:flex-end">
                                    <a href="{{ route('activites.show', $activite) }}"
                                       class="btn-icone" title="Voir le détail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if($activite->estModifiable())
                                        <form method="POST"
                                              action="{{ route('activites.destroy', $activite) }}"
                                              onsubmit="return confirm('Supprimer cette activité ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn-icone danger">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="etat-vide">
                                    <i class="bi bi-activity"></i>
                                    <p>Aucune activité trouvée</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="conteneur-pagination">
            <span>{{ $activites->total() }} activité(s) au total</span>
            {{ $activites->withQueryString()->links() }}
        </div>
    </div>

@endsection