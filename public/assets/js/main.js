/* ================================================================
   UVCI — JavaScript principal
   Fonctions partagées entre toutes les pages
   ================================================================ */

'use strict';

/* ----------------------------------------------------------------
   BARRE LATÉRALE — Ouverture / Fermeture
   ---------------------------------------------------------------- */

const barreLaterale     = document.getElementById('barre-laterale');
const boutonBasculer    = document.getElementById('bouton-basculer');
const superpositionMob  = document.getElementById('superposition-mobile');

function ouvrirBarre() {
    barreLaterale.classList.add('ouverte');
    if (superpositionMob) superpositionMob.classList.add('visible');
    document.body.style.overflow = 'hidden';
}

function fermerBarre() {
    barreLaterale.classList.remove('ouverte');
    if (superpositionMob) superpositionMob.classList.remove('visible');
    document.body.style.overflow = '';
}

function basculerBarre() {
    if (barreLaterale.classList.contains('ouverte')) {
        fermerBarre();
    } else {
        ouvrirBarre();
    }
}

if (boutonBasculer) {
    boutonBasculer.addEventListener('click', basculerBarre);
}

if (superpositionMob) {
    superpositionMob.addEventListener('click', fermerBarre);
}

/* Fermer la barre avec la touche Échap */
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape' && barreLaterale && barreLaterale.classList.contains('ouverte')) {
        fermerBarre();
    }
});

/* ----------------------------------------------------------------
   MENU DÉROULANT DU PROFIL (barre de navigation)
   ---------------------------------------------------------------- */

(function () {
    const menuProfil = document.getElementById('menu-profil-nav');
    const boutonProfil = document.getElementById('bouton-profil-nav');

    if (!menuProfil || !boutonProfil) return;

    function ouvrirMenuProfil() {
        menuProfil.classList.add('ouvert');
        boutonProfil.setAttribute('aria-expanded', 'true');
    }

    function fermerMenuProfil() {
        menuProfil.classList.remove('ouvert');
        boutonProfil.setAttribute('aria-expanded', 'false');
    }

    boutonProfil.addEventListener('click', function (e) {
        e.stopPropagation();
        if (menuProfil.classList.contains('ouvert')) {
            fermerMenuProfil();
        } else {
            ouvrirMenuProfil();
        }
    });

    document.addEventListener('click', function (e) {
        if (!menuProfil.contains(e.target)) {
            fermerMenuProfil();
        }
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') fermerMenuProfil();
    });
})();

/* ----------------------------------------------------------------
   MARQUAGE DU LIEN ACTIF selon l'URL courante
   ---------------------------------------------------------------- */

(function () {
    const pageActuelle = window.location.pathname.split('/').pop();
    const liensNav = document.querySelectorAll('.lien-navigation');

    liensNav.forEach(function (lien) {
        const href = lien.getAttribute('href') || '';
        if (href === pageActuelle || (pageActuelle === '' && href === 'tableau-de-bord.html')) {
            lien.classList.add('actif');
        }
    });
})();

/* ----------------------------------------------------------------
   RECHERCHE DANS LES TABLEAUX (filtrage côté client)
   ---------------------------------------------------------------- */

function initialiserRecherche(idChamp, idTableau) {
    const champRecherche = document.getElementById(idChamp);
    const tableau = document.getElementById(idTableau);

    if (!champRecherche || !tableau) return;

    champRecherche.addEventListener('input', function () {
        const terme = this.value.toLowerCase().trim();
        const lignes = tableau.querySelectorAll('tbody tr');

        lignes.forEach(function (ligne) {
            const texte = ligne.textContent.toLowerCase();
            ligne.style.display = texte.includes(terme) ? '' : 'none';
        });

        /* Mise à jour du compteur de résultats si présent */
        mettreAJourCompteur(tableau);
    });
}

function mettreAJourCompteur(tableau) {
    const compteur = document.getElementById('compteur-resultats');
    if (!compteur) return;

    const lignesVisibles = tableau.querySelectorAll('tbody tr:not([style*="display: none"])');
    compteur.textContent = lignesVisibles.length + ' résultat' + (lignesVisibles.length !== 1 ? 's' : '');
}

/* ----------------------------------------------------------------
   TRI DES COLONNES DE TABLEAU
   ---------------------------------------------------------------- */

function initialiserTriColonnes(idTableau) {
    const tableau = document.getElementById(idTableau);
    if (!tableau) return;

    const entetes = tableau.querySelectorAll('thead th[data-tri]');
    let colonneActive = null;
    let sensAscendant = true;

    entetes.forEach(function (entete) {
        entete.style.cursor = 'pointer';
        entete.style.userSelect = 'none';

        entete.addEventListener('click', function () {
            const colonne = this.dataset.tri;

            if (colonneActive === colonne) {
                sensAscendant = !sensAscendant;
            } else {
                colonneActive = colonne;
                sensAscendant = true;
            }

            /* Réinitialiser les indicateurs visuels */
            entetes.forEach(function (e) {
                e.querySelector('.indicateur-tri')?.remove();
            });

            /* Ajouter l'indicateur au colonne active */
            const indicateur = document.createElement('i');
            indicateur.className = 'bi indicateur-tri';
            indicateur.style.fontSize = '13px';
            indicateur.style.marginLeft = '5px';
            indicateur.className += sensAscendant ? ' bi-sort-up' : ' bi-sort-down';
            this.appendChild(indicateur);

            /* Trier les lignes */
            trierTableau(tableau, colonne, sensAscendant);
        });
    });
}

function trierTableau(tableau, colonne, ascendant) {
    const corps = tableau.querySelector('tbody');
    const lignes = Array.from(corps.querySelectorAll('tr'));

    lignes.sort(function (a, b) {
        const celluleA = a.querySelector('[data-valeur="' + colonne + '"]') || a.cells[0];
        const celluleB = b.querySelector('[data-valeur="' + colonne + '"]') || b.cells[0];
        const valeurA = (celluleA?.dataset.valeur || celluleA?.textContent || '').trim().toLowerCase();
        const valeurB = (celluleB?.dataset.valeur || celluleB?.textContent || '').trim().toLowerCase();

        const nombreA = parseFloat(valeurA);
        const nombreB = parseFloat(valeurB);

        if (!isNaN(nombreA) && !isNaN(nombreB)) {
            return ascendant ? nombreA - nombreB : nombreB - nombreA;
        }

        return ascendant
            ? valeurA.localeCompare(valeurB, 'fr')
            : valeurB.localeCompare(valeurA, 'fr');
    });

    lignes.forEach(function (ligne) { corps.appendChild(ligne); });
}

/* ----------------------------------------------------------------
   FILTRAGE PAR SÉLECTEUR
   ---------------------------------------------------------------- */

function initialiserFiltreSelecteur(idSelecteur, idTableau, indexColonne) {
    const selecteur = document.getElementById(idSelecteur);
    const tableau = document.getElementById(idTableau);

    if (!selecteur || !tableau) return;

    selecteur.addEventListener('change', function () {
        const valeur = this.value.toLowerCase();
        const lignes = tableau.querySelectorAll('tbody tr');

        lignes.forEach(function (ligne) {
            if (!valeur) {
                ligne.style.display = '';
                return;
            }
            const cellule = ligne.cells[indexColonne];
            const texte = (cellule?.textContent || '').toLowerCase();
            ligne.style.display = texte.includes(valeur) ? '' : 'none';
        });
    });
}

/* ----------------------------------------------------------------
   NOTIFICATIONS TOAST
   ---------------------------------------------------------------- */

function afficherToast(message, type) {
    type = type || 'succes';

    const couleurs = {
        succes:        { fond: '#E7FBF3', texte: '#049566', icone: 'bi-check-circle-fill' },
        erreur:        { fond: '#FDEEF0', texte: '#C42B3C', icone: 'bi-x-circle-fill' },
        avertissement: { fond: '#FEF5E0', texte: '#B07A00', icone: 'bi-exclamation-triangle-fill' },
        info:          { fond: '#E6F7FE', texte: '#1E87CC', icone: 'bi-info-circle-fill' }
    };

    const style = couleurs[type] || couleurs.info;

    const toast = document.createElement('div');
    toast.style.cssText = [
        'position:fixed', 'bottom:24px', 'right:24px', 'z-index:9999',
        'background:' + style.fond,
        'color:' + style.texte,
        'border:1px solid ' + style.texte + '33',
        'border-radius:10px', 'padding:13px 18px',
        'display:flex', 'align-items:center', 'gap:10px',
        'font-size:13.5px', 'font-weight:600',
        'font-family:var(--police, sans-serif)',
        'box-shadow:0 8px 24px rgba(0,0,0,0.12)',
        'animation:glisser-toast 0.3s ease',
        'max-width:340px'
    ].join(';');

    toast.innerHTML = '<i class="bi ' + style.icone + '" style="font-size:18px;flex-shrink:0"></i>' +
                      '<span>' + message + '</span>';

    document.body.appendChild(toast);

    setTimeout(function () {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(10px)';
        toast.style.transition = 'all 0.25s ease';
        setTimeout(function () { toast.remove(); }, 300);
    }, 3500);
}

/* Styles pour l'animation du toast */
const styleToast = document.createElement('style');
styleToast.textContent = '@keyframes glisser-toast { from { opacity:0; transform:translateY(20px); } to { opacity:1; transform:translateY(0); } }';
document.head.appendChild(styleToast);

/* ----------------------------------------------------------------
   CONFIRMATION DE SUPPRESSION
   ---------------------------------------------------------------- */

function confirmerSuppression(message, callback) {
    message = message || 'Confirmer la suppression de cet élément ?';

    const modal = document.createElement('div');
    modal.style.cssText = [
        'position:fixed', 'inset:0', 'z-index:9000',
        'background:rgba(0,0,0,0.5)',
        'display:flex', 'align-items:center', 'justify-content:center',
        'padding:16px'
    ].join(';');

    modal.innerHTML = `
        <div style="background:#fff;border-radius:14px;padding:28px 28px 24px;max-width:380px;width:100%;text-align:center;box-shadow:0 20px 60px rgba(0,0,0,0.25)">
            <div style="width:52px;height:52px;background:#FDEEF0;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px">
                <i class="bi bi-trash3" style="font-size:24px;color:#E63950"></i>
            </div>
            <h3 style="font-size:17px;font-weight:700;color:#1B2559;margin-bottom:8px">Confirmer la suppression</h3>
            <p style="font-size:13.5px;color:#8B94B2;line-height:1.5;margin-bottom:24px">${message}</p>
            <div style="display:flex;gap:10px">
                <button id="btn-annuler-modal" style="flex:1;padding:11px;border:1.5px solid #E3E8F0;border-radius:9px;font-size:14px;font-weight:600;font-family:var(--police,sans-serif);background:#fff;color:#8B94B2;cursor:pointer">Annuler</button>
                <button id="btn-confirmer-modal" style="flex:1;padding:11px;border:none;border-radius:9px;font-size:14px;font-weight:600;font-family:var(--police,sans-serif);background:#E63950;color:#fff;cursor:pointer">Supprimer</button>
            </div>
        </div>
    `;

    document.body.appendChild(modal);

    document.getElementById('btn-annuler-modal').addEventListener('click', function () {
        modal.remove();
    });

    document.getElementById('btn-confirmer-modal').addEventListener('click', function () {
        modal.remove();
        if (typeof callback === 'function') callback();
    });

    modal.addEventListener('click', function (e) {
        if (e.target === modal) modal.remove();
    });
}

/* ----------------------------------------------------------------
   FORMATAGE DES DONNÉES
   ---------------------------------------------------------------- */

function formaterNombre(valeur) {
    return new Intl.NumberFormat('fr-FR').format(valeur);
}

function formaterHeures(heures) {
    return heures.toFixed(1).replace('.', ',') + ' h';
}

/* ----------------------------------------------------------------
   INITIALISATION AU CHARGEMENT
   ---------------------------------------------------------------- */

document.addEventListener('DOMContentLoaded', function () {
    /* Initialiser la recherche si les éléments sont présents */
    initialiserRecherche('champ-recherche-tableau', 'tableau-principal');

    /* Initialiser le tri des colonnes */
    initialiserTriColonnes('tableau-principal');

    /* Initialiser les filtres des sélecteurs présents dans la page */
    const selecteurStatut     = document.getElementById('filtre-statut');
    const selecteurDepartement = document.getElementById('filtre-departement');
    const selecteurNiveau     = document.getElementById('filtre-niveau');

    if (selecteurStatut)      initialiserFiltreSelecteur('filtre-statut', 'tableau-principal', 3);
    if (selecteurDepartement) initialiserFiltreSelecteur('filtre-departement', 'tableau-principal', 4);
    if (selecteurNiveau)      initialiserFiltreSelecteur('filtre-niveau', 'tableau-principal', 3);
});
