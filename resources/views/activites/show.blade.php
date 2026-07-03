{{-- ================================================================
     FICHIER : resources/views/activites/show.blade.php
     ================================================================ --}}
@extends('layouts.app')

@section('titre', 'Détail de l\'activité')

@section('fil-ariane')
    <span>UVCI</span>
    <span class="separateur-ariane"><i class="bi bi-chevron-right"></i></span>
    <a href="{{ route('activites.index') }}" style="color:var(--couleur-texte-secondaire)">
        Activités
    </a>
    <span class="separateur-ariane"><i class="bi bi-chevron-right"></i></span>
    <span>Détail</span>
@endsection

@section('contenu')

    <div class="en-tete-page">
        <div class="groupe-titre">
            <h1 class="titre-page">Détail de l'activité</h1>
        </div>
        <div class="actions-page">
            @if($activite->statut === 'en_attente')
                <form method="POST"
                      action="{{ route('validations.valider', $activite) }}">
                    @csrf
                    <button type="submit" class="btn btn-succes">
                        <i class="bi bi-check-lg"></i>Valider
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="grille-deux-colonnes">

        <div class="carte">
            <div class="en-tete-carte">
                <div class="titre-carte">
                    <i class="bi bi-info-circle"></i>Informations
                </div>
            </div>
            <div class="corps-carte">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                    @foreach([
                        ['Enseignant', $activite->enseignant->nom_complet],
                        ['Cours', $activite->cours->intitule],
                        ['Niveau cours', $activite->cours->niveau],
                        ['Crédits', $activite->cours->nombre_credits.' crédits'],
                        ['Séquences', $activite->cours->nombre_sequences],
                        ['Niveau contenu', 'Niveau '.$activite->niveau_contenu],
                    ] as [$label, $valeur])
                        <div style="background:var(--couleur-fond);border-radius:9px;padding:11px 14px">
                            <div style="font-size:11px;font-weight:600;color:var(--couleur-texte-secondaire);
                                        text-transform:uppercase;letter-spacing:0.07em;margin-bottom:3px">
                                {{ $label }}
                            </div>
                            <div style="font-size:13.5px;font-weight:600">{{ $valeur }}</div>
                        </div>
                    @endforeach

                    <div style="background:linear-gradient(135deg,rgba(67,97,238,0.07),rgba(114,9,183,0.07));
                                border:1px solid rgba(67,97,238,0.15);border-radius:9px;padding:11px 14px;
                                grid-column:1/-1">
                        <div style="font-size:11px;font-weight:600;color:var(--couleur-principale);
                                    text-transform:uppercase;letter-spacing:0.07em;margin-bottom:4px">
                            Volume horaire calculé
                        </div>
                        <div style="font-size:32px;font-weight:700;color:var(--couleur-principale)">
                            {{ number_format($activite->volume_horaire, 1, ',', '') }} h
                        </div>
                        <div style="font-size:11.5px;color:var(--couleur-texte-secondaire);margin-top:4px">
                            {{ $activite->cours->nombre_sequences }} séquences ×
                            @php
                                $coef = \App\Models\ParametreCalcul::getCoefficient(
                                    $activite->type_action,
                                    $activite->niveau_contenu
                                );
                            @endphp
                            {{ number_format($coef, 3, ',', '') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="carte">
            <div class="en-tete-carte">
                <div class="titre-carte">
                    <i class="bi bi-clock-history"></i>Statut et historique
                </div>
            </div>
            <div class="corps-carte">

                <div style="text-align:center;margin-bottom:20px">
                    @switch($activite->statut)
                        @case('en_attente')
                            <span class="badge badge-avertissement"
                                  style="font-size:14px;padding:8px 18px">
                                <i class="bi bi-hourglass"></i> En attente de validation
                            </span>
                            @break
                        @case('valide')
                            <span class="badge badge-succes"
                                  style="font-size:14px;padding:8px 18px">
                                <i class="bi bi-check-lg"></i> Validée
                            </span>
                            @break
                        @case('rejete')
                            <span class="badge badge-danger"
                                  style="font-size:14px;padding:8px 18px">
                                <i class="bi bi-x-lg"></i> Rejetée
                            </span>
                            @break
                        @case('verrouille')
                            <span class="badge badge-neutre"
                                  style="font-size:14px;padding:8px 18px">
                                <i class="bi bi-lock"></i> Verrouillée
                            </span>
                            @break
                    @endswitch
                </div>

                <div style="display:flex;flex-direction:column;gap:10px">
                    <div style="background:var(--couleur-fond);border-radius:9px;padding:11px 14px">
                        <div style="font-size:11px;color:var(--couleur-texte-secondaire);margin-bottom:3px">
                            Type d'action
                        </div>
                        <div style="font-weight:600">
                            {{ $activite->type_action === 'creation' ? 'Création de ressources' : 'Mise à jour de ressources' }}
                        </div>
                    </div>
                    <div style="background:var(--couleur-fond);border-radius:9px;padding:11px 14px">
                        <div style="font-size:11px;color:var(--couleur-texte-secondaire);margin-bottom:3px">
                            Date de saisie
                        </div>
                        <div style="font-weight:600">
                            {{ $activite->date_saisie->format('d/m/Y à H:i') }}
                        </div>
                    </div>
                    @if($activite->date_validation)
                        <div style="background:var(--couleur-fond);border-radius:9px;padding:11px 14px">
                            <div style="font-size:11px;color:var(--couleur-texte-secondaire);margin-bottom:3px">
                                Date de validation
                            </div>
                            <div style="font-weight:600">
                                {{ $activite->date_validation->format('d/m/Y à H:i') }}
                            </div>
                        </div>
                    @endif
                    @if($activite->validateur)
                        <div style="background:var(--couleur-fond);border-radius:9px;padding:11px 14px">
                            <div style="font-size:11px;color:var(--couleur-texte-secondaire);margin-bottom:3px">
                                Validé/Rejeté par
                            </div>
                            <div style="font-weight:600">{{ $activite->validateur->name }}</div>
                        </div>
                    @endif
                    @if($activite->commentaire_rejet)
                        <div style="background:#FDEEF0;border:1px solid #FBBFC8;border-radius:9px;
                                    padding:11px 14px">
                            <div style="font-size:11px;color:var(--couleur-danger);
                                        font-weight:700;margin-bottom:4px">
                                Motif de rejet
                            </div>
                            <div style="font-size:13px;color:var(--couleur-texte-principal)">
                                {{ $activite->commentaire_rejet }}
                            </div>
                        </div>
                    @endif
                </div>

            </div>
        </div>

    </div>

@endsection