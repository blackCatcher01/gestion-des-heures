@extends('layouts.app')

@section('titre', 'Enseignants')

@section('fil-ariane')
    <span>UVCI</span>
    <span class="separateur-ariane"><i class="bi bi-chevron-right"></i></span>
    <span>Gestion</span>
    <span class="separateur-ariane"><i class="bi bi-chevron-right"></i></span>
    <span>Enseignants</span>
@endsection

@section('contenu')

    <div class="en-tete-page">
        <div class="groupe-titre">
            <h1 class="titre-page">Enseignants</h1>
            <p class="sous-titre-page">
                {{ $enseignants->total() }} enseignant(s) enregistré(s)
            </p>
        </div>
        <div class="actions-page">
            <a href="{{ route('etats.index') }}" class="btn btn-contour">
                <i class="bi bi-download"></i>Exporter
            </a>
            <a href="{{ route('enseignants.create') }}" class="btn btn-principal">
                <i class="bi bi-plus-lg"></i>Ajouter un enseignant
            </a>
        </div>
    </div>

    {{-- Mini-statistiques --}}
    <div class="grille-statistiques" style="grid-template-columns: repeat(4, 1fr); margin-bottom: 20px;">

        <div class="carte-statistique" style="padding: 16px 18px;">
            <div class="etiquette-stat">Total</div>
            <div class="valeur-stat" style="font-size:26px">{{ $stats['total'] }}</div>
            <div class="evolution-stat">Tous statuts</div>
            <i class="bi bi-people icone-fond-stat"></i>
        </div>

        <div class="carte-statistique accent-succes" style="padding: 16px 18px;">
            <div class="etiquette-stat">Permanents</div>
            <div class="valeur-stat" style="font-size:26px">{{ $stats['permanents'] }}</div>
            <div class="evolution-stat">
                {{ $stats['total'] > 0 ? round($stats['permanents'] / $stats['total'] * 100) : 0 }}%
            </div>
            <i class="bi bi-person-check icone-fond-stat"></i>
        </div>

        <div class="carte-statistique accent-avertissement" style="padding: 16px 18px;">
            <div class="etiquette-stat">Vacataires</div>
            <div class="valeur-stat" style="font-size:26px">{{ $stats['vacataires'] }}</div>
            <div class="evolution-stat">
                {{ $stats['total'] > 0 ? round($stats['vacataires'] / $stats['total'] * 100) : 0 }}%
            </div>
            <i class="bi bi-person-clock icone-fond-stat"></i>
        </div>

        <div class="carte-statistique accent-danger" style="padding: 16px 18px;">
            <div class="etiquette-stat">En attente</div>
            <div class="valeur-stat" style="font-size:26px">{{ $stats['en_attente'] }}</div>
            <div class="evolution-stat baisse"><i class="bi bi-exclamation-circle"></i> Validation</div>
            <i class="bi bi-hourglass icone-fond-stat"></i>
        </div>

    </div>

    <div class="carte">
        <div class="en-tete-carte">
            <div class="titre-carte"><i class="bi bi-table"></i>Liste des enseignants</div>
            <div class="barre-outils">
                <form method="GET" style="display:flex;gap:10px;flex-wrap:wrap">
                    <div class="champ-recherche">
                        <i class="bi bi-search"></i>
                        <input type="text" name="recherche"
                               value="{{ request('recherche') }}"
                               placeholder="Rechercher…">
                    </div>
                    <select name="statut" class="selecteur-filtre"
                            onchange="this.form.submit()">
                        <option value="">Tous les statuts</option>
                        <option value="permanent" {{ request('statut') === 'permanent' ? 'selected' : '' }}>
                            Permanent
                        </option>
                        <option value="vacataire" {{ request('statut') === 'vacataire' ? 'selected' : '' }}>
                            Vacataire
                        </option>
                    </select>
                    <select name="departement" class="selecteur-filtre"
                            onchange="this.form.submit()">
                        <option value="">Tous les départements</option>
                        @foreach($departements as $dept)
                            <option value="{{ $dept->id }}"
                                {{ request('departement') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->nom }}
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
                        <th>Enseignant</th>
                        <th class="colonne-masquable">Grade</th>
                        <th>Statut</th>
                        <th class="colonne-masquable">Département</th>
                        <th>Heures validées</th>
                        <th>Validation</th>
                        <th style="text-align:right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($enseignants as $enseignant)
                        <tr>
                            <td>
                                <div class="cellule-personne">
                                    <div class="avatar-tableau bleu">
                                        {{ strtoupper(substr($enseignant->prenom,0,1).substr($enseignant->nom,0,1)) }}
                                    </div>
                                    <div>
                                        <div class="nom-cellule">{{ $enseignant->nom_complet }}</div>
                                        <div class="detail-cellule">{{ $enseignant->user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="texte-secondaire-tableau colonne-masquable">
                                {{ ucfirst(str_replace('_', '-', $enseignant->grade)) }}
                            </td>
                            <td>
                                @if($enseignant->statut === 'permanent')
                                    <span class="badge badge-succes">
                                        <i class="bi bi-circle-fill" style="font-size:7px"></i>
                                        Permanent
                                    </span>
                                @else
                                    <span class="badge badge-avertissement">
                                        <i class="bi bi-circle-fill" style="font-size:7px"></i>
                                        Vacataire
                                    </span>
                                @endif
                            </td>
                            <td class="texte-secondaire-tableau colonne-masquable">
                                {{ $enseignant->departement->nom }}
                            </td>
                            <td>
                                <strong style="color:var(--couleur-principale)">
                                    {{ number_format($enseignant->volumeHoraireValide(), 1, ',', ' ') }} h
                                </strong>
                            </td>
                            <td>
                                @php $statutVal = $enseignant->statutValidation(); @endphp
                                @if($statutVal === 'valide')
                                    <span class="badge badge-succes"><i class="bi bi-check-lg"></i> Validé</span>
                                @elseif($statutVal === 'en_attente')
                                    <span class="badge badge-avertissement"><i class="bi bi-clock"></i> En attente</span>
                                @elseif($statutVal === 'rejete')
                                    <span class="badge badge-danger"><i class="bi bi-x-lg"></i> Rejeté</span>
                                @else
                                    <span class="texte-secondaire-tableau">—</span>
                                @endif
                            </td>
                            <td>
                                <div class="actions-tableau" style="justify-content:flex-end">
                                    <a href="{{ route('enseignants.show', $enseignant) }}"
                                       class="btn-icone" title="Voir le profil">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('enseignants.edit', $enseignant) }}"
                                       class="btn-icone" title="Modifier">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form method="POST"
                                          action="{{ route('enseignants.destroy', $enseignant) }}"
                                          onsubmit="return confirm('Supprimer cet enseignant ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-icone danger"
                                                title="Supprimer">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="etat-vide">
                                    <i class="bi bi-people"></i>
                                    <p>Aucun enseignant trouvé</p>
                                    <a href="{{ route('enseignants.create') }}"
                                       class="btn btn-principal btn-petit">
                                        <i class="bi bi-plus-lg"></i>Ajouter le premier
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="conteneur-pagination">
            <span>
                Affichage de <strong>{{ $enseignants->firstItem() ?? 0 }}</strong>
                à <strong>{{ $enseignants->lastItem() ?? 0 }}</strong>
                sur <strong>{{ $enseignants->total() }}</strong>
            </span>
            {{ $enseignants->withQueryString()->links('pagination::simple-tailwind') }}
        </div>
    </div>

@endsection