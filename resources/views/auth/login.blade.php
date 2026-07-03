<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion — UVCI</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/connexion.css') }}">
</head>
<body>

<div class="page-connexion">

    {{-- Panneau gauche --}}
    <div class="panneau-gauche">
        <div class="logo-connexion">
            <div class="logo-connexion-icone"><i class="bi bi-mortarboard-fill"></i></div>
            <div>
                <div class="logo-connexion-nom">UVCI</div>
                <div class="logo-connexion-sstitre">Gestion des heures</div>
            </div>
        </div>
        <div class="contenu-panneau">
            <h1 class="slogan-connexion">
                Gérez les heures pédagogiques <span>en toute simplicité</span>
            </h1>
            <p class="description-connexion">
                Automatisez le calcul des volumes horaires, suivez les activités
                pédagogiques et générez vos états récapitulatifs en quelques clics.
            </p>
        </div>
        <div class="statistiques-panneau">
            <div class="stat-panneau">
                <div class="valeur-stat-panneau">120+</div>
                <div class="label-stat-panneau">Enseignants</div>
            </div>
            <div class="stat-panneau">
                <div class="valeur-stat-panneau">340</div>
                <div class="label-stat-panneau">Cours actifs</div>
            </div>
            <div class="stat-panneau">
                <div class="valeur-stat-panneau">98%</div>
                <div class="label-stat-panneau">Précision</div>
            </div>
        </div>
    </div>

    {{-- Panneau droit --}}
    <div class="panneau-droite">

        <div class="en-tete-formulaire">
            <h2 class="titre-formulaire">Connexion</h2>
            <p class="sous-titre-formulaire">
                Saisissez vos identifiants pour accéder à votre espace.
            </p>
        </div>

        {{-- Erreur de connexion --}}
        @if($errors->any())
            <div class="alerte-connexion erreur" style="display:flex">
                <i class="bi bi-exclamation-circle"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            {{-- Email --}}
            <div class="groupe-champ">
                <label class="etiquette-champ" for="email">Adresse e-mail</label>
                <div class="enveloppe-champ">
                    <i class="bi bi-envelope icone-prefixe"></i>
                    <input class="champ-saisie {{ $errors->has('email') ? 'invalide' : '' }}"
                           type="email"
                           id="email"
                           name="email"
                           value="{{ old('email') }}"
                           placeholder="prenom.nom@uvci.edu.ci"
                           autocomplete="username"
                           required autofocus>
                </div>
            </div>

            {{-- Mot de passe --}}
            <div class="groupe-champ">
                <label class="etiquette-champ" for="password">Mot de passe</label>
                <div class="enveloppe-champ">
                    <i class="bi bi-lock icone-prefixe"></i>
                    <input class="champ-saisie"
                           type="password"
                           id="password"
                           name="password"
                           placeholder="••••••••"
                           autocomplete="current-password"
                           required>
                    <button type="button" class="bouton-visibilite" id="btn-voir-mdp">
                        <i class="bi bi-eye" id="icone-oeil"></i>
                    </button>
                </div>
            </div>

            {{-- Se souvenir --}}
            <div class="options-connexion">
                <label class="case-memoriser">
                    <input type="checkbox" name="remember">
                    Se souvenir de moi
                </label>
            </div>

            <button type="submit" class="bouton-connexion">
                <i class="bi bi-arrow-right-circle"></i>
                Se connecter
            </button>

        </form>
    </div>
</div>

<script>
    document.getElementById('btn-voir-mdp').addEventListener('click', function () {
        var champ = document.getElementById('password');
        var icone = document.getElementById('icone-oeil');
        if (champ.type === 'password') {
            champ.type = 'text';
            icone.className = 'bi bi-eye-slash';
        } else {
            champ.type = 'password';
            icone.className = 'bi bi-eye';
        }
    });
</script>

</body>
</html>