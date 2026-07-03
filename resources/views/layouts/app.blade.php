<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('titre', 'UVCI') — Gestion des Heures</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}">

    @stack('styles')
</head>
<body>

<div id="superposition-mobile"></div>

<div id="enveloppe">

    {{-- ═══════════════════════════════
         BARRE LATÉRALE
         ═══════════════════════════════ --}}
    <aside id="barre-laterale">

        <div class="logo-barre">
            <div class="logo-icone"><i class="bi bi-mortarboard-fill"></i></div>
            <div>
                <div class="logo-nom">UVCI</div>
                <div class="logo-sous-titre">Heures Enseignants</div>
            </div>
        </div>

        <nav class="navigation-barre" aria-label="Navigation principale">

            {{-- Menu Admin / Secrétaire --}}
            @if(auth()->user()->estAdmin() || auth()->user()->estSecretaire())

                <div class="groupe-navigation">
                    <a href="{{ route('tableau-de-bord') }}"
                       class="lien-navigation {{ request()->routeIs('tableau-de-bord') ? 'actif' : '' }}">
                        <i class="bi bi-grid-1x2"></i>Tableau de bord
                    </a>
                </div>

                <div class="groupe-navigation">
                    <div class="titre-groupe">Gestion</div>
                    <a href="{{ route('enseignants.index') }}"
                       class="lien-navigation {{ request()->routeIs('enseignants.*') ? 'actif' : '' }}">
                        <i class="bi bi-people"></i>Enseignants
                    </a>
                    <a href="{{ route('cours.index') }}"
                       class="lien-navigation {{ request()->routeIs('cours.*') ? 'actif' : '' }}">
                        <i class="bi bi-book"></i>Cours
                    </a>
                    <a href="{{ route('sequences.index') }}"
                       class="lien-navigation {{ request()->routeIs('sequences.*') ? 'actif' : '' }}">
                        <i class="bi bi-collection"></i>Séquences pédagogiques
                    </a>
                    <a href="{{ route('ressources.index') }}"
                       class="lien-navigation {{ request()->routeIs('ressources.*') ? 'actif' : '' }}">
                        <i class="bi bi-file-earmark-richtext"></i>Ressources pédagogiques
                    </a>
                </div>

                <div class="groupe-navigation">
                    <div class="titre-groupe">Suivi</div>
                    <a href="{{ route('activites.index') }}"
                       class="lien-navigation {{ request()->routeIs('activites.*') ? 'actif' : '' }}">
                        <i class="bi bi-activity"></i>Activités pédagogiques
                    </a>
                    <a href="{{ route('calcul-heures.index') }}"
                       class="lien-navigation {{ request()->routeIs('calcul-heures.*') ? 'actif' : '' }}">
                        <i class="bi bi-calculator"></i>Calcul des heures
                    </a>
                    <a href="{{ route('validations.index') }}"
                       class="lien-navigation {{ request()->routeIs('validations.*') ? 'actif' : '' }}">
                        <i class="bi bi-check2-circle"></i>
                        Validations
                        @php $nbAttente = \App\Models\ActivitePedagogique::where('statut','en_attente')->count() @endphp
                        @if($nbAttente > 0)
                            <span class="badge-nav">{{ $nbAttente }}</span>
                        @endif
                    </a>
                </div>

                <div class="groupe-navigation">
                    <div class="titre-groupe">Rapports</div>
                    <a href="{{ route('etats.index') }}"
                       class="lien-navigation {{ request()->routeIs('etats.*') ? 'actif' : '' }}">
                        <i class="bi bi-file-earmark-bar-graph"></i>États récapitulatifs
                    </a>
                </div>

                @if(auth()->user()->estAdmin())
                <div class="groupe-navigation">
                    <div class="titre-groupe">Système</div>
                    <a href="{{ route('parametres.index') }}"
                       class="lien-navigation {{ request()->routeIs('parametres.*') ? 'actif' : '' }}">
                        <i class="bi bi-gear"></i>Paramètres
                    </a>
                </div>
                @endif

            {{-- Menu Enseignant --}}
            @elseif(auth()->user()->estEnseignant())

                <div class="groupe-navigation">
                    <a href="{{ route('espace.index') }}"
                       class="lien-navigation {{ request()->routeIs('espace.index') ? 'actif' : '' }}">
                        <i class="bi bi-house"></i>Mon tableau de bord
                    </a>
                </div>
                <div class="groupe-navigation">
                    <div class="titre-groupe">Mes activités</div>
                    <a href="{{ route('espace.activites.create') }}"
                       class="lien-navigation {{ request()->routeIs('espace.activites.create') ? 'actif' : '' }}">
                        <i class="bi bi-plus-circle"></i>Déclarer une activité
                    </a>
                </div>

            @endif

        </nav>

        {{-- Profil en pied de barre --}}
        <div class="profil-barre">
            <div class="avatar-profil">
                {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
            </div>
            <div class="info-profil">
                <div class="nom-profil">{{ auth()->user()->name }}</div>
                <div class="role-profil">{{ ucfirst(auth()->user()->role) }}</div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="bouton-deconnexion" title="Se déconnecter">
                    <i class="bi bi-box-arrow-right"></i>
                </button>
            </form>
        </div>

    </aside>

    {{-- ═══════════════════════════════
         CONTENU PRINCIPAL
         ═══════════════════════════════ --}}
    <div id="contenu-principal">

        {{-- Barre de navigation --}}
        <header id="barre-navigation">
            <div class="navigation-gauche">
                <button class="bouton-basculer" id="bouton-basculer" aria-label="Basculer la barre">
                    <i class="bi bi-list"></i>
                </button>
                <nav class="fil-ariane" aria-label="Chemin de navigation">
                    @yield('fil-ariane')
                </nav>
            </div>
            <div class="navigation-droite">
                <button class="bouton-icone-nav" title="Notifications">
                    <i class="bi bi-bell"></i>
                </button>
                <div class="separateur-nav"></div>
                <div class="mini-profil-nav">
                    <div class="mini-avatar-nav">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                    <span class="mini-nom-nav">{{ auth()->user()->name }}</span>
                </div>
            </div>
        </header>

        {{-- Zone principale --}}
        <main id="zone-contenu">

            {{-- Messages flash --}}
            @if(session('succes'))
                <div style="background:#E7FBF3;border:1px solid #05C48A;border-radius:10px;
                            padding:12px 16px;margin-bottom:18px;font-size:13.5px;
                            color:#049566;display:flex;align-items:center;gap:10px">
                    <i class="bi bi-check-circle-fill"></i>
                    {{ session('succes') }}
                </div>
            @endif

            @if($errors->any())
                <div style="background:#FDEEF0;border:1px solid #E63950;border-radius:10px;
                            padding:12px 16px;margin-bottom:18px;font-size:13.5px;color:#C42B3C">
                    <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;font-weight:700">
                        <i class="bi bi-exclamation-circle-fill"></i> Erreur
                    </div>
                    <ul style="margin:0;padding-left:18px">
                        @foreach($errors->all() as $erreur)
                            <li>{{ $erreur }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('contenu')

        </main>

        <footer id="pied-page">
            © {{ date('Y') }} Université Virtuelle de Côte d'Ivoire
            — Application de gestion des heures des enseignants
        </footer>

    </div>

</div>

<script src="{{ asset('assets/js/main.js') }}"></script>
@stack('scripts')

</body>
</html>