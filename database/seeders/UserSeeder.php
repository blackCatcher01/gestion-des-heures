<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Administrateur
        \App\Models\User::create([
            'name'     => 'Admin UVCI',
            'email'    => 'admin@uvci.edu.ci',
            'password' => \Illuminate\Support\Facades\Hash::make('admin2026'),
            'role'     => 'admin',
            'actif'    => true,
        ]);

        // Secrétaire
        \App\Models\User::create([
            'name'     => 'Secrétaire UVCI',
            'email'    => 'secretaire@uvci.edu.ci',
            'password' => \Illuminate\Support\Facades\Hash::make('secret2026'),
            'role'     => 'secretaire',
            'actif'    => true,
        ]);

        // Enseignant de test
        $userEns = \App\Models\User::create([
            'name'     => 'Konan Yao Jean-Paul',
            'email'    => 'konan.yao@uvci.edu.ci',
            'password' => \Illuminate\Support\Facades\Hash::make('prof2026'),
            'role'     => 'enseignant',
            'actif'    => true,
        ]);

        \App\Models\Enseignant::create([
            'nom'            => 'Konan Yao',
            'prenom'         => 'Jean-Paul',
            'grade'          => 'maitre_assistant',
            'statut'         => 'permanent',
            'taux_horaire'   => 6500,
            'user_id'        => $userEns->id,
            'departement_id' => 1,  // INFO
        ]);

        // Année académique active
        \App\Models\AnneeAcademique::create([
            'libelle'    => '2025-2026',
            'date_debut' => '2025-10-01',
            'date_fin'   => '2026-09-30',
            'est_active' => true,
        ]);
    }
}
