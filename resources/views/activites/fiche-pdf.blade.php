<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Fiche — {{ $enseignant->nom_complet }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1B2559; }

        .entete { background: #1B2559; color: white; padding: 20px 24px; margin-bottom: 20px; }
        .entete h1 { font-size: 18px; margin-bottom: 4px; }
        .entete p { font-size: 11px; opacity: 0.75; }

        .section { margin-bottom: 18px; }
        .section-titre {
            font-size: 11px; font-weight: bold; text-transform: uppercase;
            letter-spacing: 0.08em; color: #4361EE; border-bottom: 1.5px solid #4361EE;
            padding-bottom: 5px; margin-bottom: 10px;
        }

        .grille-2 { display: table; width: 100%; margin-bottom: 16px; }
        .col { display: table-cell; width: 50%; padding-right: 14px; vertical-align: top; }
        .champ { background: #F0F2F8; border-radius: 6px; padding: 8px 11px; margin-bottom: 8px; }
        .champ-label { font-size: 9px; color: #6B7280; text-transform: uppercase; letter-spacing: 0.07em; margin-bottom: 2px; }
        .champ-valeur { font-size: 12px; font-weight: bold; }

        table { width: 100%; border-collapse: collapse; font-size: 10px; }
        thead th {
            background: #1B2559; color: white; padding: 7px 9px;
            text-align: left; font-size: 9px; text-transform: uppercase;
            letter-spacing: 0.06em;
        }
        tbody tr:nth-child(even) { background: #F8F9FE; }
        tbody td { padding: 7px 9px; border-bottom: 0.5px solid #E5E7EB; }

        .total-ligne td {
            background: #EEF2FF; font-weight: bold; font-size: 11px;
            border-top: 2px solid #4361EE; padding: 9px;
        }

        .bloc-montant {
            background: linear-gradient(135deg, #1B2559, #4361EE);
            color: white; border-radius: 8px; padding: 16px 20px;
            text-align: center; margin-top: 16px;
        }
        .montant-label { font-size: 10px; opacity: 0.75; margin-bottom: 4px; }
        .montant-valeur { font-size: 24px; font-weight: bold; }

        .pied { margin-top: 24px; padding-top: 12px; border-top: 0.5px solid #E5E7EB;
                font-size: 9px; color: #9CA3AF; text-align: center; }
    </style>
</head>
<body>

    {{-- En-tête --}}
    <div class="entete">
        <h1>Fiche récapitulative — {{ $enseignant->nom_complet }}</h1>
        <p>
            Université Virtuelle de Côte d'Ivoire ·
            Application de gestion des heures des enseignants ·
            {{ $anneeActive ? 'Année '.$anneeActive->libelle : 'Toutes années' }}
        </p>
    </div>

    {{-- Informations enseignant --}}
    <div class="section">
        <div class="section-titre">Informations personnelles</div>
        <div class="grille-2">
            <div class="col">
                <div class="champ">
                    <div class="champ-label">Nom complet</div>
                    <div class="champ-valeur">{{ $enseignant->nom_complet }}</div>
                </div>
                <div class="champ">
                    <div class="champ-label">Grade</div>
                    <div class="champ-valeur">
                        {{ ucfirst(str_replace('_', '-', $enseignant->grade)) }}
                    </div>
                </div>
                <div class="champ">
                    <div class="champ-label">Statut</div>
                    <div class="champ-valeur">{{ ucfirst($enseignant->statut) }}</div>
                </div>
            </div>
            <div class="col">
                <div class="champ">
                    <div class="champ-label">Département</div>
                    <div class="champ-valeur">{{ $enseignant->departement->nom }}</div>
                </div>
                <div class="champ">
                    <div class="champ-label">Email</div>
                    <div class="champ-valeur">{{ $enseignant->user->email }}</div>
                </div>
                <div class="champ">
                    <div class="champ-label">Taux horaire</div>
                    <div class="champ-valeur">
                        {{ number_format($enseignant->taux_horaire, 0, ',', ' ') }} FCFA/h
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Tableau des activités --}}
    <div class="section">
        <div class="section-titre">Activités pédagogiques validées</div>
        <table>
            <thead>
                <tr>
                    <th>Cours</th>
                    <th>Niveau</th>
                    <th>Type d'action</th>
                    <th>Niv. contenu</th>
                    <th>Séquences</th>
                    <th>Coefficient</th>
                    <th style="text-align:right">Volume (h)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($activites as $activite)
                    <tr>
                        <td>{{ $activite->cours->intitule }}</td>
                        <td>{{ $activite->cours->niveau }}</td>
                        <td>{{ $activite->type_action === 'creation' ? 'Création' : 'Mise à jour' }}</td>
                        <td style="text-align:center">{{ $activite->niveau_contenu }}</td>
                        <td style="text-align:center">{{ $activite->cours->nombre_sequences }}</td>
                        <td style="text-align:center">
                            @php
                                $coef = \App\Models\ParametreCalcul::getCoefficient(
                                    $activite->type_action,
                                    $activite->niveau_contenu
                                );
                            @endphp
                            {{ number_format($coef, 3, ',', '') }}
                        </td>
                        <td style="text-align:right;font-weight:bold;color:#4361EE">
                            {{ number_format($activite->volume_horaire, 1, ',', '') }}
                        </td>
                    </tr>
                @endforeach
                <tr class="total-ligne">
                    <td colspan="6">Total des heures validées</td>
                    <td style="text-align:right;color:#4361EE">
                        {{ number_format($volumeTotal, 1, ',', '') }} h
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Montant --}}
    <div class="bloc-montant">
        <div class="montant-label">
            Montant estimé à payer ({{ number_format($volumeTotal, 1, ',', '') }} h ×
            {{ number_format($enseignant->taux_horaire, 0, ',', ' ') }} FCFA)
        </div>
        <div class="montant-valeur">
            {{ number_format($montantTotal, 0, ',', ' ') }} FCFA
        </div>
    </div>

    <div class="pied">
        Document généré le {{ now()->format('d/m/Y à H:i') }} —
        UVCI Application de gestion des heures des enseignants
    </div>

</body>
</html>