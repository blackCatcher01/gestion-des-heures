@extends('layouts.app')

@section('titre', 'Tableau de bord')

@section('fil-ariane')
    <span>UVCI</span>
    <span class="separateur-ariane"><i class="bi bi-chevron-right"></i></span>
    <span>Tableau de bord</span>
@endsection

@section('contenu')

    <div class="en-tete-page">
        <div class="groupe-titre">
            <h1 class="titre-page">Tableau de bord</h1>
            <p class="sous-titre-page">
                {{ $anneeActive ? 'Année académique '.$anneeActive->libelle : 'Aucune année active' }}
                — Vue d'ensemble
            </p>
        </div>
        <div class="actions-page">
            <a href="{{ route('etats.index') }}" class="btn btn-contour">
                <i class="bi bi-download"></i>Exporter
            </a>
            <a href="{{ route('enseignants.create') }}" class="btn btn-principal">
                <i class="bi bi-plus-lg"></i>Nouvel enseignant
            </a>
        </div>
    </div>

    {{-- Statistiques --}}
    <div class="grille-statistiques">

        <div class="carte-statistique">
            <div class="etiquette-stat">Enseignants actifs</div>
            <div class="valeur-stat">{{ $stats['total_enseignants'] }}</div>
            <div class="evolution-stat">Tous statuts confondus</div>
            <i class="bi bi-people icone-fond-stat"></i>
        </div>

        <div class="carte-statistique accent-succes">
            <div class="etiquette-stat">Heures calculées</div>
            <div class="valeur-stat">{{ number_format($stats['heures_calculees'], 0, ',', ' ') }}</div>
            <div class="evolution-stat">Activités validées</div>
            <i class="bi bi-clock-history icone-fond-stat"></i>
        </div>

        <div class="carte-statistique accent-avertissement">
            <div class="etiquette-stat">Cours actifs</div>
            <div class="valeur-stat">{{ $stats['total_cours'] }}</div>
            <div class="evolution-stat">Cette année académique</div>
            <i class="bi bi-book icone-fond-stat"></i>
        </div>

        <div class="carte-statistique accent-danger">
            <div class="etiquette-stat">En attente de validation</div>
            <div class="valeur-stat">{{ $stats['en_attente'] }}</div>
            @if($stats['en_attente'] > 0)
                <div class="evolution-stat baisse">
                    <i class="bi bi-exclamation-circle"></i> Action requise
                </div>
            @else
                <div class="evolution-stat hausse">
                    <i class="bi bi-check-circle"></i> À jour
                </div>
            @endif
            <i class="bi bi-hourglass-split icone-fond-stat"></i>
        </div>

    </div>

    {{-- Graphique + Répartition --}}
    <div class="grille-trois-un">

        <div class="carte">
            <div class="en-tete-carte">
                <div class="titre-carte"><i class="bi bi-bar-chart"></i>Heures par département</div>
            </div>
            <div class="corps-carte">
                <div class="conteneur-graphique">
                    <canvas id="graphique-departements"></canvas>
                </div>
            </div>
        </div>

        <div class="carte">
            <div class="en-tete-carte">
                <div class="titre-carte"><i class="bi bi-pie-chart"></i>Répartition</div>
            </div>
            <div class="corps-carte" style="padding:16px">

                <div style="margin-bottom:20px">
                    <div style="display:flex;justify-content:space-between;font-size:12.5px;margin-bottom:6px">
                        <span style="color:var(--couleur-texte-secondaire)">Permanents</span>
                        <span style="font-weight:700">{{ $repartitionStatuts['permanents'] }}%</span>
                    </div>
                    <div style="height:6px;background:var(--couleur-bordure);border-radius:3px;overflow:hidden">
                        <div style="height:100%;width:{{ $repartitionStatuts['permanents'] }}%;background:var(--couleur-principale);border-radius:3px"></div>
                    </div>
                </div>

                <div style="margin-bottom:20px">
                    <div style="display:flex;justify-content:space-between;font-size:12.5px;margin-bottom:6px">
                        <span style="color:var(--couleur-texte-secondaire)">Vacataires</span>
                        <span style="font-weight:700">{{ $repartitionStatuts['vacataires'] }}%</span>
                    </div>
                    <div style="height:6px;background:var(--couleur-bordure);border-radius:3px;overflow:hidden">
                        <div style="height:100%;width:{{ $repartitionStatuts['vacataires'] }}%;background:var(--couleur-avertissement);border-radius:3px"></div>
                    </div>
                </div>

                @foreach([1 => ['label' => 'Niveau 1', 'couleur' => 'var(--couleur-info)'], 2 => ['label' => 'Niveau 2', 'couleur' => 'var(--couleur-succes)'], 3 => ['label' => 'Niveau 3', 'couleur' => 'var(--couleur-secondaire)']] as $niveau => $config)
                    <div style="margin-bottom:20px">
                        <div style="display:flex;justify-content:space-between;font-size:12.5px;margin-bottom:6px">
                            <span style="color:var(--couleur-texte-secondaire)">{{ $config['label'] }}</span>
                            <span style="font-weight:700">{{ $repartitionNiveaux[$niveau] }}%</span>
                        </div>
                        <div style="height:6px;background:var(--couleur-bordure);border-radius:3px;overflow:hidden">
                            <div style="height:100%;width:{{ $repartitionNiveaux[$niveau] }}%;background:{{ $config['couleur'] }};border-radius:3px"></div>
                        </div>
                    </div>
                @endforeach

            </div>
        </div>

    </div>

    <div class="grille-deux-colonnes">

        {{-- Activités récentes --}}
        <div class="carte">
            <div class="en-tete-carte">
                <div class="titre-carte"><i class="bi bi-clock"></i>Activités récentes</div>
                <a href="{{ route('activites.index') }}"
                   style="font-size:12.5px;color:var(--couleur-principale);font-weight:600">
                    Voir tout
                </a>
            </div>

            @forelse($activitesRecentes as $activite)
                <div class="activite-element">
                    <div class="icone-activite {{ $activite->statut === 'valide' ? 'validation' : 'ajout' }}">
                        <i class="bi {{ $activite->statut === 'valide' ? 'bi-check-lg' : 'bi-plus-lg' }}"></i>
                    </div>
                    <div>
                        <div class="texte-activite">
                            <strong>{{ $activite->enseignant->nom_complet }}</strong>
                            — {{ $activite->cours->intitule }}
                            ({{ $activite->volume_horaire }} h)
                        </div>
                        <div class="temps-activite">
                            {{ $activite->created_at->diffForHumans() }}
                        </div>
                    </div>
                </div>
            @empty
                <div class="etat-vide">
                    <i class="bi bi-inbox"></i>
                    <p>Aucune activité récente</p>
                </div>
            @endforelse
        </div>

        {{-- Validations en attente --}}
        <div class="carte">
            <div class="en-tete-carte">
                <div class="titre-carte"><i class="bi bi-hourglass"></i>Validations en attente</div>
                @if($stats['en_attente'] > 0)
                    <span class="badge badge-danger">{{ $stats['en_attente'] }}</span>
                @endif
            </div>

            @if($validationsEnAttente->isNotEmpty())
                <div class="conteneur-tableau">
                    <table class="tableau-donnees">
                        <thead>
                            <tr>
                                <th>Enseignant</th>
                                <th>Cours</th>
                                <th>Heures</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($validationsEnAttente as $activite)
                                <tr>
                                    <td>
                                        <div class="cellule-personne">
                                            <div class="avatar-tableau bleu">
                                                {{ strtoupper(substr($activite->enseignant->prenom, 0, 1).substr($activite->enseignant->nom, 0, 1)) }}
                                            </div>
                                            <div class="nom-cellule">{{ $activite->enseignant->nom_complet }}</div>
                                        </div>
                                    </td>
                                    <td class="texte-secondaire-tableau">{{ $activite->cours->intitule }}</td>
                                    <td><strong>{{ $activite->volume_horaire }} h</strong></td>
                                    <td>
                                        <div class="actions-tableau">
                                            <form method="POST" action="{{ route('validations.valider', $activite) }}">
                                                @csrf
                                                <button type="submit" class="btn btn-petit btn-succes">
                                                    <i class="bi bi-check-lg"></i>Valider
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="etat-vide">
                    <i class="bi bi-check-circle"></i>
                    <p>Tout est à jour</p>
                </div>
            @endif

            @if($stats['en_attente'] > 0)
                <div class="pied-carte" style="text-align:center">
                    <a href="{{ route('validations.index') }}"
                       style="font-size:12.5px;color:var(--couleur-principale);font-weight:600">
                        Gérer toutes les validations →
                    </a>
                </div>
            @endif
        </div>

    </div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const contexte = document.getElementById('graphique-departements').getContext('2d');

        new Chart(contexte, {
            type: 'bar',
            data: {
                labels: {!! json_encode(
                    $heuresParDepartement->pluck('nom')->map(function ($nom) {
                        return explode("\n", wordwrap($nom, 15, "\n"));
                    })
                ) !!},
                datasets: [{
                    label: 'Heures calculées',
                    data: {!! json_encode($heuresParDepartement->pluck('heures')) !!},
                    backgroundColor: [
                        'rgba(67, 97, 238, 0.82)',
                        'rgba(5, 196, 138, 0.82)',
                        'rgba(76, 201, 240, 0.82)',
                        'rgba(247, 183, 49, 0.82)',
                        'rgba(114, 9, 183, 0.82)',
                        'rgba(230, 57, 80, 0.82)'
                    ],
                    borderRadius: 6,
                    borderSkipped: false
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function (ctx) {
                                return ctx.parsed.y.toLocaleString('fr-FR') + ' heures';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(0,0,0,0.05)' },
                        ticks: {
                            font: { family: 'Plus Jakarta Sans, Inter, sans-serif', size: 11 },
                            color: '#8B94B2',
                            callback: function (v) { return v.toLocaleString('fr-FR'); }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            color: '#8B94B2',
                            font: {
                                family: 'Plus Jakarta Sans, Inter, sans-serif',
                                size: 11
                            },
                            maxRotation: 0,
                            minRotation: 0
                        }
                    }
                }
            }
        });
    });
</script>
@endpush
