<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification en deux étapes — UVCI</title>
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
                Une couche de sécurité <span>en plus</span>
            </h1>
            <p class="description-connexion">
                Ton compte est protégé par l'authentification à deux facteurs.
                Confirme ton identité pour continuer.
            </p>
        </div>
        <div></div>
    </div>

    {{-- Panneau droit --}}
    <div class="panneau-droite">

        <div class="en-tete-formulaire">
            <h2 class="titre-formulaire">Vérification en deux étapes</h2>
            <p class="sous-titre-formulaire">
                Saisis le code à 6 chiffres généré par ton application d'authentification.
            </p>
        </div>

        @if($errors->any())
            <div class="alerte-connexion erreur" style="display:flex">
                <i class="bi bi-exclamation-circle"></i>
                <span>{{ $errors->first() }}</span>
            </div>
        @endif

        <form method="POST" action="{{ route('two-factor.challenge.store') }}" id="formulaire-code">
            @csrf

            <div class="groupe-champ">
                <label class="etiquette-champ" for="code">Code de vérification</label>
                <div class="enveloppe-champ">
                    <i class="bi bi-shield-lock icone-prefixe"></i>
                    <input class="champ-saisie"
                           type="text"
                           id="code"
                           name="code"
                           inputmode="numeric"
                           maxlength="6"
                           placeholder="123456"
                           style="letter-spacing:4px;font-weight:700"
                           autocomplete="one-time-code"
                           autofocus>
                </div>
            </div>

            <button type="submit" class="bouton-connexion">
                <i class="bi bi-arrow-right-circle"></i>
                Vérifier
            </button>
        </form>

        <button type="button" id="btn-utiliser-recuperation" style="margin-top:16px;background:none;border:none;color:var(--couleur-principale);font-size:13px;font-weight:600;cursor:pointer;font-family:var(--police)">
            Utiliser un code de récupération à la place
        </button>

        <form method="POST" action="{{ route('two-factor.challenge.store') }}" id="formulaire-recuperation" style="display:none;margin-top:12px">
            @csrf
            <div class="groupe-champ">
                <label class="etiquette-champ" for="code_recuperation">Code de récupération</label>
                <div class="enveloppe-champ">
                    <i class="bi bi-key icone-prefixe"></i>
                    <input class="champ-saisie"
                           type="text"
                           id="code_recuperation"
                           name="code_recuperation"
                           placeholder="XXXX-XXXX"
                           autocomplete="off">
                </div>
            </div>
            <button type="submit" class="bouton-connexion">
                <i class="bi bi-arrow-right-circle"></i>
                Vérifier avec ce code
            </button>
        </form>

    </div>
</div>

<script>
    document.getElementById('btn-utiliser-recuperation').addEventListener('click', function () {
        document.getElementById('formulaire-code').style.display = 'none';
        document.getElementById('formulaire-recuperation').style.display = 'block';
        this.style.display = 'none';
    });
</script>

</body>
</html>
