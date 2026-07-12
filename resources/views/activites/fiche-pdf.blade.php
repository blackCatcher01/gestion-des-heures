<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Fiche de calcul des heures - {{ $enseignant->nom }} {{ $enseignant->prenom }}</title>
<style>
    @page {
        margin: 90px 40px 70px 40px;
    }

    * {
        box-sizing: border-box;
    }

    body {
        font-family: 'DejaVu Sans', sans-serif;
        font-size: 10px;
        color: #1a1a2e;
        line-height: 1.5;
    }

    header {
        position: fixed;
        top: -80px;
        left: 0px;
        right: 0px;
        height: 80px;
        text-align: center;
    }

    footer {
        position: fixed;
        bottom: -60px;
        left: 0px;
        right: 0px;
        height: 50px;
        font-size: 8px;
        color: #6b7280;
        text-align: center;
        border-top: 1px solid #e5e7eb;
        padding-top: 6px;
    }

    .header-table {
        width: 100%;
        border-bottom: 2px solid #1e3a8a;
        padding-bottom: 8px;
    }

    .header-table td {
        vertical-align: middle;
    }

    .header-logo {
        width: 60px;
        font-size: 22px;
        font-weight: bold;
        color: #1e3a8a;
    }

    .header-logo img {
        width: 44px;
        height: 44px;
    }

    .header-title {
        text-align: center;
    }

    .header-title .uvci {
        font-size: 13px;
        font-weight: bold;
        color: #1e3a8a;
        letter-spacing: 0.5px;
    }

    .header-title .sub {
        font-size: 8px;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .header-ref {
        width: 90px;
        text-align: right;
        font-size: 8px;
        color: #6b7280;
    }

    .doc-title {
        text-align: center;
        margin-top: 10px;
        margin-bottom: 18px;
    }

    .doc-title h1 {
        font-size: 15px;
        font-weight: bold;
        color: #1e3a8a;
        text-transform: uppercase;
        margin: 0 0 3px 0;
    }

    .doc-title .periode {
        font-size: 10px;
        color: #374151;
    }

    .identite-box {
        width: 100%;
        background-color: #f3f4f6;
        border: 1px solid #d1d5db;
        border-radius: 4px;
        padding: 10px 14px;
        margin-bottom: 16px;
    }

    .identite-box table {
        width: 100%;
    }

    .identite-box td {
        padding: 3px 0;
        font-size: 9.5px;
    }

    .identite-label {
        color: #6b7280;
        width: 130px;
        font-weight: bold;
    }

    .identite-value {
        color: #1a1a2e;
        font-weight: bold;
    }

    .section-title {
        font-size: 11px;
        font-weight: bold;
        color: #ffffff;
        background-color: #1e3a8a;
        padding: 5px 10px;
        margin-top: 16px;
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    table.data-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 10px;
    }

    table.data-table thead th {
        background-color: #dbeafe;
        color: #1e3a8a;
        font-size: 8.5px;
        font-weight: bold;
        text-transform: uppercase;
        padding: 6px 5px;
        border: 1px solid #93c5fd;
        text-align: left;
    }

    table.data-table tbody td {
        font-size: 9px;
        padding: 5px;
        border: 1px solid #e5e7eb;
        vertical-align: top;
    }

    table.data-table tbody tr:nth-child(even) {
        background-color: #f9fafb;
    }

    .text-center { text-align: center; }
    .text-right { text-align: right; }

    .badge {
        display: inline-block;
        padding: 2px 6px;
        border-radius: 8px;
        font-size: 7.5px;
        font-weight: bold;
    }
    

    .recap-table {
        width: 60%;
        margin-left: 40%;
        margin-top: 10px;
        border-collapse: collapse;
    }

    .recap-table td {
        padding: 5px 8px;
        font-size: 9.5px;
        border-bottom: 1px solid #e5e7eb;
    }

    .recap-table .label {
        color: #374151;
    }

    .recap-table .value {
        text-align: right;
        font-weight: bold;
    }

    .recap-table .total-row td {
        border-top: 2px solid #1e3a8a;
        border-bottom: none;
        font-size: 11px;
        font-weight: bold;
        color: #1e3a8a;
        padding-top: 8px;
    }

    .signatures {
        width: 100%;
        margin-top: 40px;
    }

    .signatures td {
        width: 33%;
        text-align: center;
        font-size: 9px;
        color: #374151;
        vertical-align: top;
    }

    .signature-line {
        margin-top: 45px;
        border-top: 1px solid #9ca3af;
        padding-top: 4px;
        width: 80%;
        margin-left: auto;
        margin-right: auto;
    }
</style>
</head>
<body>

    {{-- ===================== EN-TÊTE (répété sur chaque page) ===================== --}}
    <header>
        <table class="header-table">
            <tr>
                <td class="header-logo">
                    <img src="data:image/png;base64,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ACAFAQIAAFIQIAAAIAUBAgAAUhAgAAAgBQECAABSECAAACAFAQIAAFIQIAAAIAUBAgAAUhAgAAAgBQECAABSECAAACAFAQIAAFIQIAAAIAUBAgAAUhAgAAAgBQECAABSECAAACAFAQIAAFIQIAAAIAUBAgAAUhAgAAAgBQECAABSECAAACAFAQIAAFIQIAAAIAUBAgAAUhAgAAAgBQECAABSECAAACAFAQIAAFIQIAAAIAUBAgAAUhAgAAAgBQECAABSECAAACAFAQIAAFIQIAAAIAUBAgAAUhAgAAAgBQECAABSECAAACAFAQIAAFIQIAAAIAUBAgAAUhAgAAAgBQECAABSECAAACAFAQIAAFIQIAAAIAUBAgAAUhAgAAAgBQECAABSECAAACAFAQIAAFIQIAAAIAUBAgAAUhAgAAAgBQECAABSECAAACAFAQIAAFIQIAAAIAUBAgAAUhAgAAAgBQECAABSECAAACAFAQIAAFIQIAAAIAUBAgAAUhAgAAAgBQECAABSECAAACAFAQIAAFIQIAAAIAUBAgAAUhAgAAAgBQECAABSECAAACAFAQIAAFL+H1ylYOs1A27WAAAAAElFTkSuQmCC" alt="UVCI">
                </td>
                <td class="header-title">
                    <div class="uvci">Université Virtuelle de Côte d'Ivoire</div>
                    <div class="sub">Direction des Affaires Académiques et Scientifiques</div>
                </td>
                <td class="header-ref">
                    Réf. : FICHE-{{ str_pad($enseignant->id, 4, '0', STR_PAD_LEFT) }}<br>
                    Édité le {{ \Carbon\Carbon::now()->translatedFormat('d/m/Y') }}
                </td>
            </tr>
        </table>
    </header>

    <footer>
        Université Virtuelle de Côte d'Ivoire — Document généré automatiquement, sous réserve de validation par la Direction Académique.<br>
        Page <span class="pagenum"></span>
    </footer>

    {{-- ===================== TITRE ===================== --}}
    <div class="doc-title">
        <h1>Fiche individuelle de calcul des heures</h1>
        <div class="periode">
            Année académique {{ $anneeActive->libelle ?? '—' }}
        </div>
    </div>

    {{-- ===================== IDENTITÉ DE L'ENSEIGNANT ===================== --}}
    <div class="identite-box">
        <table>
            <tr>
                <td class="identite-label">Nom et prénoms</td>
                <td class="identite-value">{{ $enseignant->nom }} {{ $enseignant->prenom }}</td>
                <td class="identite-label">Grade</td>
                <td class="identite-value">{{ ucfirst($enseignant->grade) }}</td>
            </tr>
            <tr>
                <td class="identite-label">Statut</td>
                <td class="identite-value">{{ ucfirst($enseignant->statut) }}</td>
                <td class="identite-label">Département</td>
                <td class="identite-value">{{ $enseignant->departement->nom ?? '—' }}</td>
            </tr>
            <tr>
                <td class="identite-label">Téléphone</td>
                <td class="identite-value">{{ $enseignant->telephone }}</td>
                <td class="identite-label">Taux horaire</td>
                <td class="identite-value">{{ number_format($enseignant->taux_horaire, 0, ',', ' ') }} FCFA / h</td>
            </tr>
        </table>
    </div>

    {{-- ===================== TABLEAU DES ACTIVITÉS ===================== --}}
    <div class="section-title">Détail des activités pédagogiques validées</div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 26%;">Cours</th>
                <th style="width: 12%;">Filière</th>
                <th style="width: 10%;">Crédits</th>
                <th style="width: 14%;">Type d'action</th>
                <th style="width: 10%;">Niveau</th>
                <th style="width: 13%;">Date validation</th>
                <th style="width: 15%;" class="text-right">Volume horaire</th>
            </tr>
        </thead>
        <tbody>
            @forelse($activites as $activite)
            <tr>
                <td>{{ $activite->cours->intitule ?? '—' }}</td>
                <td>{{ $activite->cours->filiere ?? '—' }}</td>
                <td class="text-center">{{ $activite->cours->nombre_credits ?? '—' }}</td>
                <td>
                    <span class="badge {{ $activite->type_action === 'mise_a_jour' ? 'badge-maj' : 'badge-niveau1' }}">
                        {{ $activite->type_action === 'mise_a_jour' ? 'Mise à jour' : 'Création' }}
                    </span>
                </td>
                <td class="text-center">
                    @php
                        $niveauClass = match((int) $activite->niveau_contenu) {
                            2 => 'badge-niveau2',
                            3 => 'badge-niveau3',
                            default => 'badge-niveau1',
                        };
                    @endphp
                    <span class="badge {{ $niveauClass }}">Niveau {{ $activite->niveau_contenu }}</span>
                </td>
                <td class="text-center">
                    {{ $activite->date_validation ? $activite->date_validation->format('d/m/Y') : '—' }}
                </td>
                <td class="text-right" style="font-weight: bold;">
                    {{ number_format($activite->volume_horaire, 1, ',', ' ') }} h
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center" style="padding: 14px; color: #9ca3af;">
                    Aucune activité validée sur la période sélectionnée.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    {{-- ===================== RÉCAPITULATIF ===================== --}}
    <div class="section-title">Récapitulatif</div>

    <table class="recap-table">
        <tr>
            <td class="label">Nombre d'activités validées</td>
            <td class="value">{{ $activites->count() }}</td>
        </tr>
        <tr>
            <td class="label">Volume horaire total</td>
            <td class="value">{{ number_format($volumeTotal, 1, ',', ' ') }} h</td>
        </tr>
        <tr>
            <td class="label">Taux horaire appliqué</td>
            <td class="value">{{ number_format($enseignant->taux_horaire, 0, ',', ' ') }} FCFA</td>
        </tr>
        <tr class="total-row">
            <td class="label">MONTANT TOTAL DÛ</td>
            <td class="value">{{ number_format($montantTotal, 0, ',', ' ') }} FCFA</td>
        </tr>
    </table>

    {{-- ===================== SIGNATURES ===================== --}}
    <table class="signatures">
        <tr>
            <td>
                <div class="signature-line">L'enseignant</div>
            </td>
            <td>
                <div class="signature-line">Le responsable pédagogique</div>
            </td>
            <td>
                <div class="signature-line">La Direction Académique</div>
            </td>
        </tr>
    </table>

</body>
</html>