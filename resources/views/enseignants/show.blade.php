@extends('layouts.app')

@section('titre', $enseignant->nom_complet)

@section('fil-ariane')
    <span>UVCI</span>
    <span class="separateur-ariane"><i class="bi bi-chevron-right"></i></span>
    <a href="{{ route('enseignants.index') }}" style="color:var(--couleur-texte-secondaire)">
        Enseignants
    </a>
    <span class="separateur-ariane"><i class="bi bi-chevron-right"></i></span>
    <span>{{ $enseignant->nom_complet }}</span>
@endsection

@section('contenu')

    <div class="en-tete-page">
        <div class="groupe-titre">
            <h1 class="titre-page">Profil enseignant</h1>
        </div>
        <div class="actions-page">
            <a href="{{ route('enseignants.edit', $enseignant) }}" class="btn btn-contour">
                <i class="bi bi-pencil"></i>Modifier
            </a>
            <a href="{{ route('etats.pdf', $enseignant) }}"
               class="btn btn-principal" target="_blank">
                <i class="bi bi-file-earmark-pdf"></i>Télécharger la fiche
            </a>
        </div>
    </div>

    {{-- Carte profil --}}
    <div style="background:linear-gradient(135deg,#1B2559,#4361EE);border-radius:var(--rayon-carte);
                padding:28px 30px;display:flex;align-items:center;gap:22px;
                margin-bottom:22px;color:#fff;flex-wrap:wrap">
        <div style="width:68px;height:68px;border-radius:18px;background:rgba(255,255,255,0.18);
                    display:flex;align-items:center;justify-content:center;font-size:24px;
                    font-weight:700;flex-shrink:0;border:2px solid rgba(255,255,255,0.3)">
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
                <span style="font-size:11px;padding:3px 11px;border-radius:20px;
                             background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.25)">
                    <i class="bi bi-envelope"></i> {{ $enseignant->user->email }}
                </span>
                @if($enseignant->telephone)
                    <span style="font-size:11px;padding:3px 11px;border-radius:20px;
                                 background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.25)">
                        <i class="bi bi-telephone"></i> {{ $enseignant->telephone }}
                    </span>
                @endif
                <span style="font-size:11px;padding:3px 11px;border-radius:20px;
                             background:rgba(255,255,255,0.15);border:1px solid rgba(255,255,255,0.25)">
                    <i class="bi bi-cash"></i>
                    {{ number_format($enseignant->taux_horaire, 0, ',', ' ') }} FCFA/h
                </span>
            </div>
        </div>
        <div style="display:flex;gap:22px;flex-shrink:0">
            <div style="text-align:center">
                <div style="font-size:28px;font-weight:700">
                    {{ number_format($volumeTotal, 1, ',', ' ') }}
                </div>
                <div style="font-size:10px;opacity:0.65;margin-top:3px">Heures validées</div>
            </div>
            <div style="text-align:center">
                <div style="font-size:28px;font-weight:700">
                    {{ $enseignant->activites->count() }}
                </div>
                <div style="font-size:10px;opacity:0.65;margin-top:3px">Activités</div>
            </div>
            <div style="text-align:center">
                <div style="font-size:28px;font-weight:700;color:#05C48A">
                    {{ number_format(max(0, $volumeTotal - 200), 1, ',', '') }}
                </div>
                <div style="font-size:10px;opacity:0.65;margin-top:3px">H. complémentaires</div>
            </div>
        </div>
    </div>

    <div class="grille-deux-colonnes">

        {{-- Informations détaillées --}}
        <div class="carte">
            <div class="en-tete-carte">
                <div class="titre-carte"><i class="bi bi-person-lines-fill"></i>Informations</div>
            </div>
            <div class="corps-carte">
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                    @foreach([
                        ['Nom', $enseignant->nom],
                        ['Prénom', $enseignant->prenom],
                        ['Grade', ucfirst(str_replace('_','-',$enseignant->grade))],
                        ['Statut', ucfirst($enseignant->statut)],
                        ['Département', $enseignant->departement->nom],
                        ['Taux horaire', number_format($enseignant->taux_horaire,0,',',' ').' FCFA/h'],
                        ['Email', $enseignant->user->email],
                        ['Téléphone', $enseignant->telephone ?? '—'],
                    ] as [$label, $valeur])
                        <div style="background:var(--couleur-fond);border-radius:9px;padding:11px 14px">
                            <div style="font-size:11px;font-weight:600;color:var(--couleur-texte-secondaire);
                                        text-transform:uppercase;letter-spacing:0.07em;margin-bottom:3px">
                                {{ $label }}
                            </div>
                            <div style="font-size:13.5px;font-weight:600;color:var(--couleur-texte-principal)">
                                {{ $valeur }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Activités récentes --}}
        <div class="carte">
            <div class="en-tete-carte">
                <div class="titre-carte"><i class="bi bi-activity"></i>Activités déclarées</div>
                <a href="{{ route('activites.index') }}?enseignant={{ $enseignant->id }}"
                   style="font-size:12.5px;color:var(--couleur-principale);font-weight:600">
                    Voir tout
                </a>
            </div>
            @forelse($enseignant->activites->take(6) as $act)
                <div style="display:flex;align-items:center;gap:12px;padding:12px 20px;
                            border-bottom:1px solid var(--couleur-bordure)">
                    <div style="width:8px;height:8px;border-radius:50%;flex-shrink:0;
                                background:{{ ['en_attente'=>'#F7B731','valide'=>'#05C48A','rejete'=>'#E63950','verrouille'=>'#6B7280'][$act->statut] }}">
                    </div>
                    <div style="flex:1">
                        <div style="font-size:13px;font-weight:600">{{ $act->cours->intitule }}</div>
                        <div style="font-size:11.5px;color:var(--couleur-texte-secondaire)">
                            {{ $act->type_action === 'creation' ? 'Création' : 'Mise à jour' }} ·
                            Niveau {{ $act->niveau_contenu }}
                        </div>
                    </div>
                    <strong style="color:var(--couleur-principale)">
                        {{ number_format($act->volume_horaire, 1, ',', '') }} h
                    </strong>
                </div>
            @empty
                <div class="etat-vide">
                    <i class="bi bi-inbox"></i>
                    <p>Aucune activité déclarée</p>
                </div>
            @endforelse
            @if($enseignant->activites->count() > 6)
                <div class="pied-carte" style="text-align:center">
                    <span style="font-size:12.5px;color:var(--couleur-texte-secondaire)">
                        + {{ $enseignant->activites->count() - 6 }} autres activités
                    </span>
                </div>
            @endif
        </div>

    </div>

@endsection