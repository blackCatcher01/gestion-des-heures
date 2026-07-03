# Gestion des Heures — UVCI

Application web développée avec **Laravel** permettant de gérer, suivre et calculer automatiquement les heures d'enseignement des enseignants de l'Université Virtuelle de Côte d'Ivoire (UVCI).

L'application facilite la déclaration des activités pédagogiques, le calcul du volume horaire, la validation hiérarchique ainsi que la génération de fiches individuelles en PDF et d'états récapitulatifs en Excel.

---

## Fonctionnalités

- Gestion des enseignants
- Gestion des départements
- Gestion des années académiques
- Gestion des cours
- Gestion des séquences pédagogiques
- Gestion des ressources pédagogiques
- Déclaration des activités pédagogiques
- Validation ou rejet des activités
- Calcul automatique des volumes horaires
- Tableau de bord avec statistiques
- Génération de fiches individuelles en PDF
- Export des états récapitulatifs en Excel
- Gestion des utilisateurs et des rôles

---

## Stack technique

- **Framework :** Laravel 13
- **Langage :** PHP 8.3+
- **Base de données :** MySQL (par défaut), compatible SQLite et PostgreSQL
- **Interface :** Blade, Bootstrap
- **PDF :** barryvdh/laravel-dompdf
- **Excel :** maatwebsite/excel

---

## Installation

### 1. Cloner le projet

```bash
git clone https://github.com/blackCatcher01/gestion-des-heures.git
cd gestion-des-heures
```

### 2. Installer les dépendances

```bash
composer install
```

### 3. Créer le fichier d'environnement

```bash
cp .env.example .env
```

### 4. Générer la clé de l'application

```bash
php artisan key:generate
```

### 5. Configurer la base de données

L'application utilise **MySQL** par défaut.

Modifier les informations de connexion dans le fichier `.env` :

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gestion_heures
DB_USERNAME=root
DB_PASSWORD=
```

> Il est également possible d'utiliser SQLite ou PostgreSQL en modifiant les variables `DB_CONNECTION` et `DB_*`.

### 6. Exécuter les migrations et les seeders

```bash
php artisan migrate --seed
```

### 7. Démarrer le serveur

```bash
php artisan serve
```

L'application sera accessible à l'adresse :

```
http://localhost:8000
```

---

## Comptes de démonstration

| Rôle | Email | Mot de passe |
|------|-------|--------------|
| Administrateur | admin@uvci.edu.ci | admin2026 |
| Secrétaire | secretaire@uvci.edu.ci | secret2026 |
| Enseignant | konan.yao@uvci.edu.ci | prof2026 |

Les données de démonstration comprennent :

- une année académique active (2025–2026) ;
- un département **INFO** ;
- les coefficients de calcul de base.

---

## Gestion des rôles

### Administrateur

Dispose d'un accès complet à l'application :

- gestion des utilisateurs ;
- gestion des départements ;
- gestion des années académiques ;
- gestion des coefficients de calcul ;
- accès à toutes les fonctionnalités métier.

### Secrétaire

Peut gérer :

- les enseignants ;
- les cours ;
- les séquences pédagogiques ;
- les ressources pédagogiques ;
- les activités pédagogiques ;
- les validations ;
- le calcul des heures ;
- les états récapitulatifs.

Le secrétaire n'a pas accès aux paramètres de l'application.

### Enseignant

Dispose d'un espace personnel permettant de :

- déclarer ses activités pédagogiques ;
- consulter son volume horaire ;
- télécharger sa fiche individuelle.

---

## Calcul du volume horaire

Le volume horaire d'une activité pédagogique est calculé automatiquement selon la formule suivante :

```text
Volume horaire = Nombre de séquences × Coefficient
```

Le coefficient dépend de deux paramètres :

### Type d'action

- Création
- Mise à jour

### Niveau de contenu

- Niveau 1 : contenus simples
- Niveau 2 : contenus interactifs
- Niveau 3 : simulations

Les coefficients sont entièrement paramétrables par l'administrateur depuis le menu **Paramètres**.

---

## Modèle de données

Les principales relations sont les suivantes :

- Un **Enseignant** appartient à un **Département**.
- Un **Cours** appartient à une **Année académique**.
- Un **Cours** possède plusieurs **Séquences pédagogiques**.
- Une **Séquence pédagogique** possède plusieurs **Ressources pédagogiques**.
- Une **Activité pédagogique** est réalisée par un enseignant sur un cours.
- Une activité peut être associée à une ou plusieurs ressources pédagogiques.
- Une activité suit le workflow suivant :

```text
En attente
      │
      ▼
  Validée
      ou
  Rejetée
```

---

## Routes principales

| Route | Accès | Description |
|--------|--------|-------------|
| `/tableau-de-bord` | Admin, Secrétaire | Tableau de bord |
| `/enseignants` | Admin, Secrétaire | Gestion des enseignants |
| `/cours` | Admin, Secrétaire | Gestion des cours |
| `/sequences` | Admin, Secrétaire | Gestion des séquences pédagogiques |
| `/ressources` | Admin, Secrétaire | Gestion des ressources pédagogiques |
| `/activites` | Admin, Secrétaire | Déclaration et suivi des activités |
| `/validations` | Admin, Secrétaire | Validation des activités |
| `/calcul-heures` | Admin, Secrétaire | Calcul du volume horaire |
| `/etats-recapitulatifs` | Admin, Secrétaire | Exports PDF et Excel |
| `/parametres` | Admin | Gestion des paramètres |
| `/espace-enseignant` | Enseignant | Espace personnel |

---

## Commandes utiles

Réinitialiser complètement la base de données :

```bash
php artisan migrate:fresh --seed
```

Lister les routes :

```bash
php artisan route:list
```

Vider les caches Laravel :

```bash
php artisan optimize:clear
```