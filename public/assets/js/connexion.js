/* ================================================================
   UVCI — JavaScript de la page de connexion
   ================================================================ */

'use strict';

document.addEventListener('DOMContentLoaded', function () {

    /* --- Éléments du formulaire --- */
    const formulaire        = document.getElementById('formulaire-connexion');
    const champEmail        = document.getElementById('champ-email');
    const champMotDePasse   = document.getElementById('champ-mot-de-passe');
    const boutonConnexion   = document.getElementById('bouton-connexion');
    const alerteErreur      = document.getElementById('alerte-erreur');
    const boutonVisibilite  = document.getElementById('bouton-visibilite-mdp');
    const iconeVisibilite   = document.getElementById('icone-visibilite');

    /* --- Basculement de visibilité du mot de passe --- */
    if (boutonVisibilite && champMotDePasse) {
        boutonVisibilite.addEventListener('click', function () {
            const visible = champMotDePasse.type === 'text';
            champMotDePasse.type = visible ? 'password' : 'text';
            iconeVisibilite.className = visible ? 'bi bi-eye' : 'bi bi-eye-slash';
        });
    }

    /* --- Validation du champ e-mail --- */
    function validerEmail(valeur) {
        const regexEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regexEmail.test(valeur);
    }

    function afficherErreurChamp(champ, idMessageErreur, message) {
        champ.classList.add('invalide');
        const messageEl = document.getElementById(idMessageErreur);
        if (messageEl) {
            messageEl.textContent = message;
            messageEl.classList.add('visible');
        }
    }

    function effacerErreurChamp(champ, idMessageErreur) {
        champ.classList.remove('invalide');
        const messageEl = document.getElementById(idMessageErreur);
        if (messageEl) messageEl.classList.remove('visible');
    }

    /* Validation en temps réel sur l'email */
    if (champEmail) {
        champEmail.addEventListener('blur', function () {
            if (!this.value.trim()) {
                afficherErreurChamp(this, 'erreur-email', 'L\'adresse e-mail est obligatoire.');
            } else if (!validerEmail(this.value)) {
                afficherErreurChamp(this, 'erreur-email', 'Veuillez saisir une adresse e-mail valide.');
            } else {
                effacerErreurChamp(this, 'erreur-email');
            }
        });

        champEmail.addEventListener('input', function () {
            if (this.classList.contains('invalide')) {
                effacerErreurChamp(this, 'erreur-email');
            }
        });
    }

    /* Validation en temps réel sur le mot de passe */
    if (champMotDePasse) {
        champMotDePasse.addEventListener('input', function () {
            if (this.classList.contains('invalide')) {
                effacerErreurChamp(this, 'erreur-mot-de-passe');
            }
        });
    }

    /* --- Gestion de la soumission du formulaire --- */
    if (formulaire) {
        formulaire.addEventListener('submit', function (e) {
            e.preventDefault();

            const email      = champEmail ? champEmail.value.trim() : '';
            const motDePasse = champMotDePasse ? champMotDePasse.value : '';
            let formulaireValide = true;

            /* Masquer l'alerte précédente */
            if (alerteErreur) alerteErreur.style.display = 'none';

            /* Validation de l'email */
            if (!email) {
                afficherErreurChamp(champEmail, 'erreur-email', 'L\'adresse e-mail est obligatoire.');
                formulaireValide = false;
            } else if (!validerEmail(email)) {
                afficherErreurChamp(champEmail, 'erreur-email', 'Veuillez saisir une adresse e-mail valide.');
                formulaireValide = false;
            }

            /* Validation du mot de passe */
            if (!motDePasse) {
                afficherErreurChamp(champMotDePasse, 'erreur-mot-de-passe', 'Le mot de passe est obligatoire.');
                formulaireValide = false;
            }

            if (!formulaireValide) return;

            /* Démarrer l'état de chargement */
            demarrerChargement();

            /* Simulation de l'authentification (à remplacer par l'appel API Laravel) */
            setTimeout(function () {
                arreterChargement();

                /* Comptes de démonstration */
                const comptesDemo = [
                    { email: 'admin@uvci.edu.ci', motDePasse: 'admin2026', role: 'Administrateur' },
                    { email: 'secretaire@uvci.edu.ci', motDePasse: 'secret2026', role: 'Secrétaire' },
                    { email: 'enseignant@uvci.edu.ci', motDePasse: 'prof2026', role: 'Enseignant' }
                ];

                const compteValide = comptesDemo.find(function (compte) {
                    return compte.email === email && compte.motDePasse === motDePasse;
                });

                if (compteValide) {
                    /* Redirection vers le tableau de bord */
                    window.location.href = 'tableau-de-bord.html';
                } else {
                    /* Afficher l'erreur */
                    if (alerteErreur) {
                        alerteErreur.style.display = 'flex';
                        alerteErreur.textContent = '';
                        alerteErreur.innerHTML = '<i class="bi bi-exclamation-circle"></i><span>Identifiants incorrects. Vérifiez votre adresse e-mail et votre mot de passe.</span>';
                    }
                    if (champEmail)      champEmail.classList.add('invalide');
                    if (champMotDePasse) champMotDePasse.classList.add('invalide');
                }
            }, 1400);
        });
    }

    /* --- États du bouton de connexion --- */
    function demarrerChargement() {
        if (!boutonConnexion) return;
        boutonConnexion.disabled = true;
        const texte    = boutonConnexion.querySelector('.texte-bouton');
        const icone    = boutonConnexion.querySelector('.icone-chargement');
        const iconeOK  = boutonConnexion.querySelector('.icone-normale');
        if (texte)    texte.textContent = 'Connexion en cours…';
        if (icone)    icone.style.display = 'inline-block';
        if (iconeOK)  iconeOK.style.display = 'none';
    }

    function arreterChargement() {
        if (!boutonConnexion) return;
        boutonConnexion.disabled = false;
        const texte    = boutonConnexion.querySelector('.texte-bouton');
        const icone    = boutonConnexion.querySelector('.icone-chargement');
        const iconeOK  = boutonConnexion.querySelector('.icone-normale');
        if (texte)    texte.textContent = 'Se connecter';
        if (icone)    icone.style.display = 'none';
        if (iconeOK)  iconeOK.style.display = 'inline-block';
    }
});
