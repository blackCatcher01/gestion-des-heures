# Gestion des Heures — UVCI

Application de gestion et de calcul automatisé des heures d'enseignement pour les enseignants de l'UVCI (déclaration d'activités pédagogiques, calcul du volume horaire, validation hiérarchique, états récapitulatifs).

## Stack technique

- **PHP** 8.3+ / **Laravel** 13.8
- **Base de données** : SQLite par défaut (configurable en MySQL/PostgreSQL via `.env`)
- **barryvdh/laravel-dompdf** — génération des fiches PDF
- **maatwebsite/excel** — exports Excel

## Installation

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
php artisan serve
```

L'application est alors disponible sur `http://localhost:8000`.

> Si vous préférez MySQL/PostgreSQL, modifiez `DB_CONNECTION` et les variables `DB_*` dans `.env` avant `php artisan migrate`.

## Comptes de test (créés par le seeder)

| Rôle | Email | Mot de passe |
|---|---|---|
| Administrateur | admin@uvci.edu.ci | admin2026 |
| Secrétaire | secretaire@uvci.edu.ci | secret2026 |
| Enseignant (Konan Yao Jean-Paul) | konan.yao@uvci.edu.ci | prof2026 |

Une année académique 2025-2026 est créée active par défaut, ainsi que le département INFO et les coefficients de calcul de base.

## Rôles et accès

- **admin** : accès complet, y compris les Paramètres (années, coefficients, départements, comptes utilisateurs).
- **secretaire** : gestion des enseignants, cours, séquences, ressources, activités, validations, calcul des heures, états récapitulatifs — sans accès aux Paramètres.
- **enseignant** : accès à son Espace enseignant uniquement (déclaration de ses activités, consultation de son volume horaire, téléchargement de sa fiche).

## Fonctionnement du calcul des heures

Le volume horaire d'une activité pédagogique est calculé automatiquement selon la formule :

```
volume_horaire = nombre_sequences (du cours) x coefficient (type_action + niveau_contenu)
```

Les coefficients sont paramétrables dans Paramètres -> Coefficients de calcul et dépendent de deux critères :
- type_action : creation ou mise_a_jour
- niveau_contenu : 1 (contenus simples), 2 (interactifs), 3 (simulations)

## Modèle de données — points clés

- Enseignant appartient à un Departement et réalise des ActivitePedagogique.
- ActivitePedagogique est rattachée à une AnneeAcademique (via le Cours concerné) et peut porter sur une ou plusieurs RessourcePedagogique (champ activite_id, optionnel — à renseigner manuellement lors de la création/modification d'une ressource).
- Cours structure des SequencePedagogique, qui contiennent des RessourcePedagogique.
- Une activité passe par les statuts en_attente -> valide / rejete (workflow de validation).

## Routes principales

| Route | Accès | Description |
|---|---|---|
| /tableau-de-bord | admin, secretaire | Vue d'ensemble et statistiques |
| /enseignants | admin, secretaire | Gestion des enseignants |
| /cours | admin, secretaire | Gestion des cours |
| /sequences | admin, secretaire | Gestion des séquences pédagogiques |
| /ressources | admin, secretaire | Gestion des ressources pédagogiques |
| /activites | admin, secretaire | Déclaration et suivi des activités |
| /validations | admin, secretaire | Validation/rejet des activités en attente |
| /calcul-heures | admin, secretaire | Calcul et détail du volume horaire par enseignant |
| /etats-recapitulatifs | admin, secretaire | Exports (fiches PDF, état global Excel) |
| /parametres | admin | Années, coefficients, départements, comptes |
| /espace-enseignant | enseignant | Auto-déclaration et suivi personnel |

## Commandes utiles

```bash
php artisan migrate:fresh --seed   # réinitialiser la base avec les données de démo
php artisan route:list             # lister les routes
php artisan optimize:clear         # vider les caches après modification de config/routes
```

## Notes de version

- La colonne `ressources_pedagogiques.activite_id` (association "porter" du MCD) a été ajoutée par la migration `2026_07_03_192730_add_activite_id_to_ressources_pedagogiques_table`. Elle est nullable : les ressources créées avant cette migration ne sont pas rattachées automatiquement à une activité.
