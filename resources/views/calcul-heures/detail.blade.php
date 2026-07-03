@extends('layouts.app')

@section('titre', 'Détail du calcul — ' . $enseignant->nom_complet)

@section('fil-ariane')
    <span>UVCI</span>
    <span class="separateur-ariane"><i class="bi bi-chevron-right"></i></span>
    <a href="{{ route('calcul-heures.index') }}" style="color:inherit;text-decoration:none">Calcul des heures</a>
    <span class="separateur-ariane"><i class="bi bi-chevron-right"></i></span>
    <span>{{ $enseignant->nom_complet }}</span>
@endsection

@section('contenu')

    <div class="en-tete-page">
        <div class="groupe-titre">
            <a href="{{ route('calcul-heures.index') }}"
               style="font-size:12.5px;color:var(--couleur-texte-secondaire);text-decoration:none;
                      display:inline-flex;align-items:center;gap:4px;margin-bottom:8px">
                <i class="bi bi-arrow-left"></i> Retour au calcul des heures
            </a>
            <h1 class="titre-page">{{ $enseignant->nom_complet }}</h1>
            <p class="sous-titre-page">
                {{ ucfirst(str_replace('_', '-', $enseignant->grade)) }} ·
                {{ ucfirst($enseignant->statut) }} ·
                {{ $enseignant->departement->nom }}
                @if($anneeActive)
                    · Année {{ $anneeActive->libelle }}
                @endif
            </p>
        </div>
        <div class="actions-page">
            <a href="{{ route('etats.pdf', $enseignant) }}" class="btn btn-contour" target="_blank">
                <i class="bi bi-file-earmark-pdf"></i>Télécharger la fiche PDF
            </a>
        </div>
    </div>

    {{-- Résumé --}}
    <div class="barre-totaux">
        <div class="item-total">
            <div class="valeur-total">{{ number_format($volumeTotal, 1, ',', '') }} h</div>
            <div class="label-total">Volume horaire validé</div>
        </div>
        <div class="separateur-total"></div>
        <div class="item-total">
            <div class="valeur-total">{{ number_format($enseignant->taux_horaire, 0, ',', ' ') }}</div>
            <div class="label-total">Taux horaire (FCFA/h)</div>
        </div>
        <div class="separateur-total"></div>
        <div class="item-total">
            <div class="valeur-total" style="color:var(--couleur-succes)">
                {{ number_format($montantTotal, 0, ',', ' ') }}
            </div>
            <div class="label-total">Montant estimé (FCFA)</div>
        </div>
        <div class="separateur-total"></div>
        <div class="item-total">
            <div class="valeur-total">{{ $activites->count() }}</div>
            <div class="label-total">Activités validées</div>
        </div>
    </div>

    {{-- Détail des activités --}}
    <div class="carte">
        <div class="en-tete-carte">
            <div class="titre-carte"><i class="bi bi-calculator"></i>Détail du calcul</div>
        </div>

        @if($activites->isNotEmpty())
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
                        @foreach($activites as $act)
                            @php
                                $coef = \App\Models\ParametreCalcul::getCoefficient(
                                    $act->type_action, $act->niveau_contenu
                                );
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
                            <td colspan="5" style="color:var(--couleur-texte-secondaire);padding:12px 16px">
                                Total — {{ $enseignant->nom_complet }}
                            </td>
                            <td style="text-align:right;font-size:17px;color:var(--couleur-principale);
                                       padding:12px 16px;border-top:2px solid rgba(67,97,238,0.2)">
                                {{ number_format($volumeTotal, 1, ',', '') }} h
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @else
            <div class="etat-vide">
                <i class="bi bi-inbox"></i>
                <p>Aucune activité validée {{ $anneeActive ? 'pour '.$anneeActive->libelle : '' }}</p>
            </div>
        @endif
    </div>

@endsection
