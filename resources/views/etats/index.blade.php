@extends('layouts.app')

@section('titre', 'États récapitulatifs')

@section('fil-ariane')
    <span>UVCI</span>
    <span class="separateur-ariane"><i class="bi bi-chevron-right"></i></span>
    <span>Rapports</span>
    <span class="separateur-ariane"><i class="bi bi-chevron-right"></i></span>
    <span>États récapitulatifs</span>
@endsection

@section('contenu')

    <div class="en-tete-page">
        <div class="groupe-titre">
            <h1 class="titre-page">États récapitulatifs</h1>
            <p class="sous-titre-page">
                Fiches individuelles et états de paiement ·
                {{ $anneeActive ? 'Année '.$anneeActive->libelle : 'Aucune année active' }}
            </p>
        </div>
        <div class="actions-page">
            <a href="{{ route('etats.excel') }}" class="btn btn-succes">
                <i class="bi bi-file-earmark-excel"></i>Export Excel global
            </a>
        </div>
    </div>

    {{-- Totaux globaux --}}
    <div class="barre-totaux">
        <div class="item-total">
            <div class="valeur-total">{{ $totalEnseignants }}</div>
            <div class="label-total">Enseignants actifs</div>
        </div>
        <div class="separateur-total"></div>
        <div class="item-total">
            <div class="valeur-total">{{ number_format($totalHeures, 0, ',', ' ') }}</div>
            <div class="label-total">Heures totales calculées</div>
        </div>
        <div class="separateur-total"></div>
        <div class="item-total">
            <div class="valeur-total">{{ $totalCours }}</div>
            <div class="label-total">Cours actifs</div>
        </div>
        <div class="separateur-total"></div>
        <div class="item-total">
            <div class="valeur-total">{{ $activitesValidees }}</div>
            <div class="label-total">Activités validées</div>
        </div>
        <div class="separateur-total"></div>
        <div class="item-total">
            <div class="valeur-total" style="color:var(--couleur-succes)">{{ $tauxValidation }}%</div>
            <div class="label-total">Taux de validation</div>
        </div>
    </div>

    {{-- Cartes d'export --}}
    <div style="font-size:14px;font-weight:700;color:var(--couleur-texte-principal);margin-bottom:14px">
        Générer un document
    </div>
    <div class="grille-exports">
        <a href="#fiches-individuelles" class="carte-export" style="text-decoration:none">
            <div class="icone-export export-fiche"><i class="bi bi-person-lines-fill"></i></div>
            <div class="titre-export">Fiches individuelles</div>
            <div class="desc-export">
                Fiche récapitulative par enseignant avec le détail de toutes ses activités
                et son volume horaire total.
            </div>
            <span class="btn btn-principal" style="width:100%;justify-content:center">
                <i class="bi bi-file-earmark-person"></i>Choisir un enseignant
            </span>
        </a>
        <a href="{{ route('etats.excel') }}" class="carte-export" style="text-decoration:none">
            <div class="icone-export export-excel"><i class="bi bi-table"></i></div>
            <div class="titre-export">État global des heures</div>
            <div class="desc-export">
                Tableau consolidé de tous les enseignants avec leurs volumes horaires,
                classé par département.
            </div>
            <span class="btn btn-succes" style="width:100%;justify-content:center">
                <i class="bi bi-file-earmark-excel"></i>Générer (Excel)
            </span>
        </a>
    </div>

    {{-- Tableau --}}
    <div class="carte" id="fiches-individuelles">
        <div class="en-tete-carte">
            <div class="titre-carte"><i class="bi bi-people"></i>Fiches par enseignant</div>
            <div class="barre-outils">
                <form method="GET" style="display:flex;gap:10px">
                    <select name="departement" class="selecteur-filtre" onchange="this.form.submit()">
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
                        <th class="colonne-masquable">Département</th>
                        <th>Heures validées</th>
                        <th class="colonne-masquable">Taux (FCFA/h)</th>
                        <th>Montant estimé</th>
                        <th style="text-align:right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($enseignants as $enseignant)
                        @php
                            $heures  = $enseignant->heures_validees ?? 0;
                            $montant = $heures * $enseignant->taux_horaire;
                        @endphp
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
                                {{ ucfirst(str_replace('_','-',$enseignant->grade)) }}
                            </td>
                            <td class="texte-secondaire-tableau colonne-masquable">
                                {{ $enseignant->departement->nom }}
                            </td>
                            <td>
                                <strong style="color:var(--couleur-principale)">
                                    {{ number_format($heures, 1, ',', '') }} h
                                </strong>
                            </td>
                            <td class="texte-secondaire-tableau colonne-masquable">
                                {{ number_format($enseignant->taux_horaire, 0, ',', ' ') }}
                            </td>
                            <td>
                                <strong>
                                    {{ number_format($montant, 0, ',', ' ') }} FCFA
                                </strong>
                            </td>
                            <td>
                                <div class="actions-tableau" style="justify-content:flex-end;gap:5px">
                                    <a href="{{ route('etats.pdf', $enseignant) }}"
                                       class="btn-icone" title="Télécharger la fiche PDF"
                                       target="_blank">
                                        <i class="bi bi-file-earmark-pdf"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="etat-vide">
                                    <i class="bi bi-people"></i>
                                    <p>Aucun enseignant trouvé</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="conteneur-pagination">
            <span>{{ $enseignants->total() }} enseignant(s)</span>
            {{ $enseignants->withQueryString()->links() }}
        </div>
    </div>

@endsection