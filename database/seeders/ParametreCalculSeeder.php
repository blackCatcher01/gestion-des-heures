<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ParametreCalculSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $coefficients = [
            ['type_action' => 'creation',    'niveau_contenu' => 1, 'coefficient' => 0.400],
            ['type_action' => 'creation',    'niveau_contenu' => 2, 'coefficient' => 0.750],
            ['type_action' => 'creation',    'niveau_contenu' => 3, 'coefficient' => 1.500],
            ['type_action' => 'mise_a_jour', 'niveau_contenu' => 1, 'coefficient' => 0.200],
            ['type_action' => 'mise_a_jour', 'niveau_contenu' => 2, 'coefficient' => 0.375],
            ['type_action' => 'mise_a_jour', 'niveau_contenu' => 3, 'coefficient' => 0.750],
        ];

        foreach ($coefficients as $coef) {
            \App\Models\ParametreCalcul::create(array_merge(
                $coef,
                ['sequences_par_credit' => 40]
            ));
        }
    }
}
