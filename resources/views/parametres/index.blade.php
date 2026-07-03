@extends('layouts.app')

@section('titre', 'Paramètres')

@section('fil-ariane')
    <span>UVCI</span>
    <span class="separateur-ariane"><i class="bi bi-chevron-right"></i></span>
    <span>Système</span>
    <span class="separateur-ariane"><i class="bi bi-chevron-right"></i></span>
    <span>Paramètres</span>
@endsection

@section('contenu')

    <div class="en-tete-page">
        <div class="groupe-titre">
            <h1 class="titre-page">Paramètres système</h1>
            <p class="sous-titre-page">Réservé à l'administrateur</p>
        </div>
    </div>

    <div style="background:#FEF5E0;border:1px solid #F7B731;border-radius:10px;
                padding:12px 16px;margin-bottom:20px;font-size:13px;color:#92610A;
                display:flex;gap:10px;align-items:center">
        <i class="bi bi-shield-lock" style="font-size:18px;flex-shrink:0"></i>
        <span>Toute modification des coefficients impacte directement les calculs futurs.</span>
    </div>

    {{-- Années académiques --}}
    <div class="carte">
        <div class="onglets">
            <div class="onglet actif" onclick="changerOngletParam('annees', this)">
                <i class="bi bi-calendar3"></i><span class="label-onglet">Années académiques</span>
            </div>
            <div class="onglet" onclick="changerOngletParam('coefficients', this)">
                <i class="bi bi-calculator"></i><span class="label-onglet">Coefficients de calcul</span>
            </div>
            <div class="onglet" onclick="changerOngletParam('departements', this)">
                <i class="bi bi-diagram-3"></i><span class="label-onglet">Départements</span>
            </div>
            <div class="onglet" onclick="changerOngletParam('comptes', this)">
                <i class="bi bi-people-fill"></i><span class="label-onglet">Comptes utilisateurs</span>
            </div>
        </div>

        <div class="contenu-onglet actif" id="tab-annees">
        <div class="corps-carte">
            @foreach($annees as $annee)
                <div style="border:{{ $annee->est_active ? '2px solid var(--couleur-principale)' : '1px solid var(--couleur-bordure)' }};
                            border-radius:12px;padding:16px 18px;margin-bottom:12px;
                            background:{{ $annee->est_active ? 'rgba(67,97,238,0.04)' : 'var(--couleur-carte)' }};
                            position:relative">
                    @if($annee->est_active)
                        <span style="position:absolute;top:-10px;left:14px;background:var(--couleur-principale);
                                     color:#fff;font-size:10px;font-weight:700;padding:2px 9px;border-radius:10px">
                            Active
                        </span>
                    @endif
                    <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:10px">
                        <div>
                            <div style="font-size:16px;font-weight:700">{{ $annee->libelle }}</div>
                            <div style="font-size:12.5px;color:var(--couleur-texte-secondaire);margin-top:2px">
                                {{ $annee->date_debut->format('d/m/Y') }} →
                                {{ $annee->date_fin->format('d/m/Y') }}
                            </div>
                        </div>
                        <div style="display:flex;gap:8px">
                            @if(!$annee->est_active)
                                <form method="POST"
                                      action="{{ route('annees.activer', $annee) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-contour btn-petit">
                                        <i class="bi bi-check-circle"></i>Activer
                                    </button>
                                </form>
                                <form method="POST"
                                      action="{{ route('annees.cloturer', $annee) }}"
                                      onsubmit="return confirm('Clôturer cette année ? Les données seront verrouillées.')">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-petit">
                                        <i class="bi bi-lock"></i>Clôturer
                                    </button>
                                </form>
                            @else
                                <span class="badge badge-succes">En cours</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach

            <form method="POST" action="{{ route('annees.store') }}"
                  style="display:flex;gap:10px;flex-wrap:wrap;margin-top:14px">
                @csrf
                <input type="text" name="libelle" placeholder="Ex. : 2026-2027"
                       style="padding:8px 12px;border:1.5px solid var(--couleur-bordure);
                              border-radius:var(--rayon-element);font-size:13px;
                              font-family:var(--police);outline:none" required>
                <input type="date" name="date_debut"
                       style="padding:8px 12px;border:1.5px solid var(--couleur-bordure);
                              border-radius:var(--rayon-element);font-size:13px;
                              font-family:var(--police);outline:none" required>
                <input type="date" name="date_fin"
                       style="padding:8px 12px;border:1.5px solid var(--couleur-bordure);
                              border-radius:var(--rayon-element);font-size:13px;
                              font-family:var(--police);outline:none" required>
                <button type="submit" class="btn btn-principal btn-petit">
                    <i class="bi bi-plus-lg"></i>Créer
                </button>
            </form>
        </div>
        </div>

        <div class="contenu-onglet" id="tab-coefficients">
    {{-- Coefficients --}}
        <div style="font-size:14px;font-weight:700;color:var(--couleur-texte-principal);margin-bottom:16px">
            Coefficients de calcul
        </div>
        <div class="conteneur-tableau">
            <table class="tableau-donnees">
                <thead>
                    <tr>
                        <th>Type d'action</th>
                        <th>Niveau de contenu</th>
                        <th>Description</th>
                        <th>Coefficient</th>
                        <th style="text-align:right">Modifier</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($coefficients as $param)
                        <tr>
                            <td>
                                @if($param->type_action === 'creation')
                                    <span class="badge badge-info">Création</span>
                                @else
                                    <span class="badge badge-violet">Mise à jour</span>
                                @endif
                            </td>
                            <td><strong>Niveau {{ $param->niveau_contenu }}</strong></td>
                            <td class="texte-secondaire-tableau">
                                @switch($param->niveau_contenu)
                                    @case(1) Contenus simples + quiz + évaluations @break
                                    @case(2) Niveau 1 + 25% activités interactives @break
                                    @case(3) Serious games, simulations haute qualité @break
                                @endswitch
                            </td>
                            <td>
                                <form method="POST"
                                      action="{{ route('parametres.coefficients.update', $param) }}"
                                      style="display:flex;align-items:center;gap:8px">
                                    @csrf
                                    @method('PUT')
                                    <input type="number" name="coefficient"
                                           value="{{ $param->coefficient }}"
                                           step="0.005" min="0"
                                           style="width:90px;padding:6px 10px;
                                                  border:1.5px solid var(--couleur-bordure);
                                                  border-radius:6px;font-size:13px;
                                                  font-family:var(--police);text-align:center;
                                                  outline:none">
                                    <button type="submit" class="btn-icone" title="Sauvegarder">
                                        <i class="bi bi-check-lg"></i>
                                    </button>
                                </form>
                            </td>
                            <td></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        </div>

        <div class="contenu-onglet" id="tab-departements">
    {{-- Départements --}}
        <div style="font-size:14px;font-weight:700;color:var(--couleur-texte-principal);margin-bottom:16px">
            Départements
        </div>
        <div class="conteneur-tableau">
            <table class="tableau-donnees">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Code</th>
                        <th>Enseignants</th>
                        <th style="text-align:right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($departements as $dept)
                        <tr>
                            <td><strong>{{ $dept->nom }}</strong></td>
                            <td class="texte-secondaire-tableau">{{ $dept->code }}</td>
                            <td>{{ $dept->enseignants_count }}</td>
                            <td>
                                <div class="actions-tableau" style="justify-content:flex-end">
                                    <form method="POST"
                                          action="{{ route('departements.destroy', $dept) }}"
                                          onsubmit="return confirm('Supprimer ce département ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn-icone danger">
                                            <i class="bi bi-trash3"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="pied-carte">
            <form method="POST" action="{{ route('departements.store') }}"
                  style="display:flex;gap:10px;flex-wrap:wrap">
                @csrf
                <input type="text" name="nom" placeholder="Nom du département"
                       style="flex:1;padding:8px 12px;border:1.5px solid var(--couleur-bordure);
                              border-radius:var(--rayon-element);font-size:13px;
                              font-family:var(--police);outline:none" required>
                <input type="text" name="code" placeholder="Code (ex: INFO)"
                       style="width:120px;padding:8px 12px;border:1.5px solid var(--couleur-bordure);
                              border-radius:var(--rayon-element);font-size:13px;
                              font-family:var(--police);outline:none" required>
                <button type="submit" class="btn btn-principal btn-petit">
                    <i class="bi bi-plus-lg"></i>Ajouter
                </button>
            </form>
        </div>
        </div>

        <div class="contenu-onglet" id="tab-comptes">
    {{-- Comptes utilisateurs --}}
        <div style="font-size:14px;font-weight:700;color:var(--couleur-texte-principal);margin-bottom:16px">
            Comptes utilisateurs
        </div>
        <div class="conteneur-tableau">
            <table class="tableau-donnees">
                <thead>
                    <tr>
                        <th>Utilisateur</th>
                        <th>Rôle</th>
                        <th class="colonne-masquable">Email</th>
                        <th>Statut</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($utilisateurs as $user)
                        <tr>
                            <td>
                                <div class="cellule-personne">
                                    <div class="avatar-tableau bleu">
                                        {{ strtoupper(substr($user->name,0,2)) }}
                                    </div>
                                    <div class="nom-cellule">{{ $user->name }}</div>
                                </div>
                            </td>
                            <td>
                                @switch($user->role)
                                    @case('admin')
                                        <span class="badge badge-danger">Administrateur</span>
                                        @break
                                    @case('secretaire')
                                        <span class="badge badge-violet">Secrétaire</span>
                                        @break
                                    @case('enseignant')
                                        <span class="badge badge-info">Enseignant</span>
                                        @break
                                @endswitch
                            </td>
                            <td class="texte-secondaire-tableau colonne-masquable">
                                {{ $user->email }}
                            </td>
                            <td>
                                @if($user->actif)
                                    <span class="badge badge-succes">
                                        <i class="bi bi-circle-fill" style="font-size:7px"></i>
                                        Actif
                                    </span>
                                @else
                                    <span class="badge badge-neutre">Désactivé</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        </div>

    </div>

@endsection

@push('scripts')
<script>
function changerOngletParam(nom, el) {
    document.querySelectorAll('#tab-annees, #tab-coefficients, #tab-departements, #tab-comptes')
        .forEach(function (t) { t.classList.remove('actif'); });
    document.getElementById('tab-' + nom).classList.add('actif');

    el.parentElement.querySelectorAll('.onglet').forEach(function (o) {
        o.classList.remove('actif');
    });
    el.classList.add('actif');
}
</script>
@endpush