<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departements = [
            ['nom' => 'Informatique et Technologies',  'code' => 'INFO'],
            ['nom' => 'Mathématiques et Sciences',     'code' => 'MATH'],
            ['nom' => 'Sciences de Gestion',           'code' => 'GEST'],
            ['nom' => 'Droit et Sciences Politiques',  'code' => 'DROIT'],
            ['nom' => 'Langues et Communication',      'code' => 'LANG'],
        ];

        foreach ($departements as $dept) {
            \App\Models\Departement::create($dept);
        }
    }
}
