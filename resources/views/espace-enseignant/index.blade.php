@extends('layouts.app')

@section('titre', 'Mon espace')

@section('fil-ariane')
    <span>UVCI</span>
    <span class="separateur-ariane"><i class="bi bi-chevron-right"></i></span>
    <span>Mon espace</span>
@endsection

@section('contenu')

    {{-- Carte profil hero --}}
    <div style="background:linear-gradient(135deg,#1B2559 0%,#4361EE 100%);
                border-radius:var(--rayon-carte);padding:28px 30px;
                display:flex;align-items:center;gap:22px;margin-bottom:22px;
                color:#fff;flex-wrap:wrap">
        <div style="width:72px;height:72px;border-radius:18px;
                    background:rgba(255,255,255,0.18);display:flex;align-items:center;
                    justify-content:center;font-size:26px;font-weight:700;flex-shrink:0;
                    border:2px solid rgba(255,255,255,0.3)">
            {{ strtoupper(substr($enseignant->prenom,0,1).substr($enseignant->nom,0,1)) }}
        </div>
        <div style="flex:1">
            <div style="font-size:22px;font-weight:700;margin-bottom:4px">
                {{ $enseignant->nom_complet }}
            </div>
            <div style="font-size:13px;opacity:0.75;margin-bottom:10px">
                {{ ucfirst(str_replace('_','-',$enseignant->grade)) }} ·
                {{ $enseignant->departement->nom }} ·
                {{ ucfirst($enseignant->statut) }}
            </div>
            <div style="display:flex;gap:8px;flex-wrap:wrap">
                <span style="font-size:11px;font-weight:600;padding:3px 11px;border-radius:20px;
                             background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.25)">
                    <i class="bi bi-calendar3"></i>
                    {{ $anneeActive ? 'Année '.$anneeActive->libelle : 'Aucune année active' }}
                </span>
                <span style="font-size:11px;font-weight:600;padding:3px 11px;border-radius:20px;
                             background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.25)">
                    <i class="bi bi-cash"></i>
                    {{ number_format($enseignant->taux_horaire, 0, ',', ' ') }} FCFA/heure
                </span>
            </div>
        </div>
        <div style="display:flex;gap:24px;flex-shrink:0;flex-wrap:wrap">
            <div style="text-align:center">
                <div style="font-size:28px;font-weight:700;line-height:1">
                    {{ number_format($volumeValide, 1, ',', ' ') }}
                </div>
                <div style="font-size:10px;opacity:0.65;margin-top:4px">Heures validées</div>
            </div>
            <div style="text-align:center">
                <div style="font-size:28px;font-weight:700;line-height:1">
                    {{ $activites->count() }}
                </div>
                <div style="font-size:10px;opacity:0.65;margin-top:4px">Déclarations</div>
            </div>
            <div style="text-align:center">
                <div style="font-size:28px;font-weight:700;line-height:1">
                    {{ $stats['cours_actifs'] }}
                </div>
                <div style="font-size:10px;opacity:0.65;margin-top:4px">Cours actifs</div>
            </div>
            <div style="text-align:center">
                <div style="font-size:28px;font-weight:700;line-height:1">
                    {{ $stats['en_attente'] }}
                </div>
                <div style="font-size:10px;opacity:0.65;margin-top:4px">En attente</div>
            </div>
        </div>
    </div>

    {{-- Bouton déclarer --}}
    <div style="display:flex;gap:10px;margin-bottom:22px;flex-wrap:wrap">
        <a href="{{ route('espace.activites.create') }}" class="btn btn-principal">
            <i class="bi bi-plus-lg"></i>Déclarer une activité
        </a>
        <a href="{{ route('espace.fiche.pdf') }}" class="btn btn-contour" target="_blank">
            <i class="bi bi-file-earmark-pdf"></i>Télécharger ma fiche
        </a>
    </div>

    {{-- Grille principale --}}
    <div class="grille-trois-un" style="margin-bottom:22px">

        {{-- Mes activités déclarées --}}
        <div class="carte">
            <div class="en-tete-carte">
                <div class="titre-carte"><i class="bi bi-activity"></i>Mes activités déclarées</div>
            </div>

            @forelse($activites as $activite)
                <div style="display:flex;align-items:flex-start;gap:14px;padding:16px 22px;
                            border-bottom:1px solid var(--couleur-bordure);
                            {{ $activite->statut === 'en_attente' ? 'border-left:3px solid var(--couleur-avertissement)' : '' }}
                            {{ $activite->statut === 'rejete' ? 'border-left:3px solid var(--couleur-danger);background:#FFFBFB' : '' }}">

                    <div style="width:40px;height:40px;border-radius:10px;display:flex;
                                align-items:center;justify-content:center;font-size:18px;flex-shrink:0;
                                {{ $activite->type_action === 'creation' ? 'background:#EAF0FE;color:#4361EE' : 'background:#F2EAFE;color:#7209B7' }}">
                        <i class="bi {{ $activite->type_action === 'creation' ? 'bi-plus-circle' : 'bi-arrow-repeat' }}"></i>
                    </div>

                    <div style="flex:1">
                        <div style="font-size:14px;font-weight:600;color:var(--couleur-texte-principal);margin-bottom:3px">
                            {{ $activite->cours->intitule }}
                        </div>
                        <div style="font-size:12px;color:var(--couleur-texte-secondaire)">
                            {{ $activite->cours->niveau }} ·
                            {{ $activite->cours->nombre_credits }} crédit(s) ·
                            {{ $activite->type_action === 'creation' ? 'Création' : 'Mise à jour' }} ·
                            Niveau {{ $activite->niveau_contenu }}
                        </div>
                        @if($activite->statut === 'rejete' && $activite->commentaire_rejet)
                            <div style="margin-top:7px;font-size:12px;color:var(--couleur-danger);
                                        background:#FDEEF0;border-radius:6px;padding:6px 10px">
                                <i class="bi bi-exclamation-circle"></i>
                                Motif : {{ $activite->commentaire_rejet }}
                            </div>
                        @endif
                    </div>

                    <div style="text-align:right;flex-shrink:0">
                        <div style="font-size:20px;font-weight:700;color:var(--couleur-principale)">
                            {{ number_format($activite->volume_horaire, 1, ',', '') }} h
                        </div>
                        <div style="margin-top:5px">
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
                            @endswitch
                        </div>
                        @if($activite->estModifiable())
                            <div style="margin-top:6px">
                                <a href="{{ route('espace.activites.edit', $activite) }}"
                                   style="font-size:11.5px;color:var(--couleur-principale);font-weight:600">
                                    Modifier →
                                </a>
                            </div>
                        @endif
                    </div>

                </div>
            @empty
                <div class="etat-vide">
                    <i class="bi bi-inbox"></i>
                    <p>Vous n'avez encore déclaré aucune activité</p>
                    <a href="{{ route('espace.activites.create') }}" class="btn btn-principal btn-petit">
                        <i class="bi bi-plus-lg"></i>Faire ma première déclaration
                    </a>
                </div>
            @endforelse
        </div>

        {{-- Panneau droit --}}
        <div>
            {{-- Volume horaire --}}
            <div class="carte" style="margin-bottom:16px">
                <div class="en-tete-carte">
                    <div class="titre-carte"><i class="bi bi-clock"></i>Mon volume horaire</div>
                </div>
                <div class="corps-carte">
                    <div style="text-align:center;margin-bottom:16px">
                        <div style="font-size:42px;font-weight:700;color:var(--couleur-principale);line-height:1">
                            {{ number_format($volumeValide, 0, ',', ' ') }}
                        </div>
                        <div style="font-size:13px;color:var(--couleur-texte-secondaire);margin-top:4px">
                            heures validées cette année
                        </div>
                    </div>
                    <div style="display:grid;grid-template-columns:1fr;gap:10px">
                        <div style="background:var(--couleur-fond);border-radius:9px;padding:11px;text-align:center">
                            <div style="font-size:18px;font-weight:700;color:var(--couleur-principale)">
                                {{ number_format($stats['montant_estime'], 0, ',', ' ') }}
                            </div>
                            <div style="font-size:11px;color:var(--couleur-texte-secondaire)">FCFA estimé</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Mes cours --}}
            <div class="carte">
                <div class="en-tete-carte">
                    <div class="titre-carte"><i class="bi bi-book"></i>Mes cours</div>
                </div>
                <div style="padding:0">
                    @forelse($mesCours as $ligne)
                        <div style="padding:13px 16px;border-bottom:1px solid var(--couleur-bordure);
                                    display:flex;justify-content:space-between;align-items:center">
                            <div>
                                <div style="font-size:13px;font-weight:600">{{ $ligne['cours']->intitule }}</div>
                                <div style="font-size:11px;color:var(--couleur-texte-secondaire)">
                                    {{ $ligne['cours']->niveau }} — {{ $ligne['cours']->nombre_credits }} crédits
                                </div>
                            </div>
                            <span class="badge badge-succes" style="font-size:10px">
                                {{ number_format($ligne['heures'], 1, ',', '') }} h
                            </span>
                        </div>
                    @empty
                        <div class="etat-vide" style="padding:20px">
                            <i class="bi bi-book"></i>
                            <p>Aucun cours validé pour l'instant</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

@endsection