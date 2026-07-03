@extends('layouts.app')

@section('titre', 'Ajouter un cours')

@section('fil-ariane')
    <span>UVCI</span>
    <span class="separateur-ariane"><i class="bi bi-chevron-right"></i></span>
    <a href="{{ route('cours.index') }}" style="color:var(--couleur-texte-secondaire)">Cours</a>
    <span class="separateur-ariane"><i class="bi bi-chevron-right"></i></span>
    <span>Ajouter</span>
@endsection

@section('contenu')

    <div class="en-tete-page">
        <div class="groupe-titre">
            <h1 class="titre-page">Ajouter un cours</h1>
            <p class="sous-titre-page">
                Le nombre de séquences est calculé automatiquement (1 crédit = 40 séquences)
            </p>
        </div>
    </div>

    <div class="carte" style="max-width:700px">
        <div class="corps-carte">
            <form method="POST" action="{{ route('cours.store') }}">
                @csrf

                @php
                $champStyle = "padding:9px 12px;border:1.5px solid var(--couleur-bordure);
                               border-radius:var(--rayon-element);font-size:13.5px;
                               font-family:var(--police);outline:none;width:100%;background:#F8FAFF";
                @endphp

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:20px">

                    <div style="display:flex;flex-direction:column;gap:5px;grid-column:1/-1">
                        <label style="font-size:12.5px;font-weight:600">Intitulé du cours *</label>
                        <input type="text" name="intitule"
                               value="{{ old('intitule') }}" required
                               style="{{ $champStyle }}"
                               placeholder="Ex. : Programmation Web">
                    </div>

                    <div style="display:flex;flex-direction:column;gap:5px">
                        <label style="font-size:12.5px;font-weight:600">Filière *</label>
                        <input type="text" name="filiere"
                               value="{{ old('filiere') }}" required
                               style="{{ $champStyle }}"
                               placeholder="Ex. : DAS, IEA…">
                    </div>

                    <div style="display:flex;flex-direction:column;gap:5px">
                        <label style="font-size:12.5px;font-weight:600">Niveau *</label>
                        <select name="niveau" required style="{{ $champStyle }};appearance:none;cursor:pointer">
                            <option value="">Sélectionner…</option>
                            @foreach(['L1','L2','L3','M1','M2'] as $niv)
                                <option value="{{ $niv }}" {{ old('niveau') === $niv ? 'selected' : '' }}>
                                    {{ $niv }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div style="display:flex;flex-direction:column;gap:5px">
                        <label style="font-size:12.5px;font-weight:600">Semestre *</label>
                        <select name="semestre" required style="{{ $champStyle }};appearance:none;cursor:pointer">
                            <option value="">Sélectionner…</option>
                            <option value="1" {{ old('semestre') == 1 ? 'selected' : '' }}>Semestre 1</option>
                            <option value="2" {{ old('semestre') == 2 ? 'selected' : '' }}>Semestre 2</option>
                        </select>
                    </div>

                    <div style="display:flex;flex-direction:column;gap:5px">
                        <label style="font-size:12.5px;font-weight:600">Nombre de crédits *</label>
                        <select name="nombre_credits" required
                                style="{{ $champStyle }};appearance:none;cursor:pointer"
                                onchange="majSequences(this.value)">
                            <option value="">Sélectionner…</option>
                            @foreach([1,2,3,4] as $cr)
                                <option value="{{ $cr }}" {{ old('nombre_credits') == $cr ? 'selected' : '' }}>
                                    {{ $cr }} crédit(s)
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div style="display:flex;flex-direction:column;gap:5px">
                        <label style="font-size:12.5px;font-weight:600">Séquences calculées</label>
                        <div id="affichage-sequences"
                             style="{{ $champStyle }};background:rgba(67,97,238,0.05);
                                    color:var(--couleur-principale);font-weight:700;cursor:default">
                            — (sélectionner les crédits)
                        </div>
                    </div>

                    <div style="display:flex;flex-direction:column;gap:5px;grid-column:1/-1">
                        <label style="font-size:12.5px;font-weight:600">Année académique *</label>
                        <select name="annee_academique_id" required
                                style="{{ $champStyle }};appearance:none;cursor:pointer">
                            <option value="">Sélectionner…</option>
                            @foreach($annees as $annee)
                                <option value="{{ $annee->id }}"
                                    {{ ($annee->est_active || old('annee_academique_id') == $annee->id) ? 'selected' : '' }}>
                                    {{ $annee->libelle }}
                                    {{ $annee->est_active ? '(active)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                </div>

                <div style="display:flex;gap:10px;justify-content:flex-end">
                    <a href="{{ route('cours.index') }}" class="btn btn-contour">Annuler</a>
                    <button type="submit" class="btn btn-principal">
                        <i class="bi bi-check-lg"></i>Enregistrer
                    </button>
                </div>

            </form>
        </div>
    </div>

@endsection

@push('scripts')
<script>
function majSequences(credits) {
    var div = document.getElementById('affichage-sequences');
    if (!credits) {
        div.textContent = '— (sélectionner les crédits)';
    } else {
        var seq = parseInt(credits) * 40;
        div.textContent = seq + ' séquences';
    }
}
</script>
@endpush