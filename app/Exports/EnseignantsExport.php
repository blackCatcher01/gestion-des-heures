<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use App\Models\Enseignant;
use App\Models\AnneeAcademique;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class EnseignantsExport implements
    FromCollection,
    WithHeadings,
    WithMapping,
    WithStyles,
    WithTitle,
    ShouldAutoSize
{
    use Exportable;

    private $anneeActive;

    public function __construct()
    {
        $this->anneeActive = AnneeAcademique::active();
    }

    public function collection()
    {
        return Enseignant::with(['departement', 'user', 'activites.cours'])
            ->get();
    }

    public function headings(): array
    {
        return [
            'N°',
            'Nom',
            'Prénom',
            'Grade',
            'Statut',
            'Département',
            'Email',
            'Taux horaire (FCFA)',
            'Heures validées',
            'Heures complémentaires',
            'Montant estimé (FCFA)',
        ];
    }

    public function map($enseignant): array
    {
        static $index = 0;
        $index++;

        $heures = $enseignant->volumeHoraireValide($this->anneeActive?->id);
        $chargeStandard = 200; // heures standard — à paramétrer
        $heuresCompl = max(0, $heures - $chargeStandard);
        $montant = $heures * $enseignant->taux_horaire;

        return [
            $index,
            $enseignant->nom,
            $enseignant->prenom,
            ucfirst(str_replace('_', '-', $enseignant->grade)),
            ucfirst($enseignant->statut),
            $enseignant->departement->nom,
            $enseignant->user->email,
            number_format($enseignant->taux_horaire, 0, ',', ' '),
            number_format($heures, 1, ',', ' '),
            number_format($heuresCompl, 1, ',', ' '),
            number_format($montant, 0, ',', ' '),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            // Ligne d'en-tête en gras avec fond bleu
            1 => [
                'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill'      => ['fillType' => 'solid', 'color' => ['rgb' => '1B2559']],
                'alignment' => ['horizontal' => 'center'],
            ],
        ];
    }

    public function title(): string
    {
        return 'État global des heures';
    }
}