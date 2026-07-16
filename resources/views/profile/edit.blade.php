@extends('layouts.app')

@section('titre', 'Mon compte')

@section('fil-ariane')
    <span>UVCI</span>
    <span class="separateur-ariane"><i class="bi bi-chevron-right"></i></span>
    <span>Mon compte</span>
@endsection

@section('contenu')

    @php
        $cs = "padding:9px 12px;border:1.5px solid var(--couleur-bordure);
               border-radius:var(--rayon-element);font-size:13.5px;
               font-family:var(--police);outline:none;width:100%;background:#F8FAFF";
    @endphp

    <div class="en-tete-page">
        <div class="groupe-titre">
            <h1 class="titre-page">Mon compte</h1>
            <p style="font-size:13px;color:var(--couleur-texte-secondaire);margin-top:4px">
                Gère tes informations personnelles, ton mot de passe et la sécurité de ton compte.
            </p>
        </div>
    </div>

    @if(session('status') === 'profile-updated')
        <div style="background:#E7FBF3;border:1px solid #05C48A;border-radius:10px;padding:12px 16px;margin-bottom:18px;font-size:13.5px;color:#049566;display:flex;align-items:center;gap:10px">
            <i class="bi bi-check-circle-fill"></i> Tes informations ont bien été mises à jour.
        </div>
    @elseif(session('status') === 'password-updated')
        <div style="background:#E7FBF3;border:1px solid #05C48A;border-radius:10px;padding:12px 16px;margin-bottom:18px;font-size:13.5px;color:#049566;display:flex;align-items:center;gap:10px">
            <i class="bi bi-check-circle-fill"></i> Ton mot de passe a bien été modifié.
        </div>
    @elseif(session('status') === 'two-factor-authentication-disabled')
        <div style="background:#FEF5E0;border:1px solid #F7B731;border-radius:10px;padding:12px 16px;margin-bottom:18px;font-size:13.5px;color:#B07A00;display:flex;align-items:center;gap:10px">
            <i class="bi bi-shield-slash-fill"></i> L'authentification à deux facteurs a été désactivée.
        </div>
    @endif

    <div style="display:flex;flex-direction:column;gap:20px;max-width:700px">

        {{-- ═══════════ INFORMATIONS PERSONNELLES ═══════════ --}}
        <div class="carte" id="informations">
            <div class="corps-carte">
                <h2 style="font-size:15px;font-weight:700;margin-bottom:4px">Informations personnelles</h2>
                <p style="font-size:12.5px;color:var(--couleur-texte-secondaire);margin-bottom:18px">
                    Ton nom et ton adresse e-mail utilisés pour te connecter.
                </p>

                <form method="POST" action="{{ route('profile.update') }}">
                    @csrf
                    @method('PATCH')

                    <div style="display:flex;flex-direction:column;gap:14px">
                        <div style="display:flex;flex-direction:column;gap:5px">
                            <label style="font-size:12.5px;font-weight:600">Nom complet *</label>
                            <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                                   style="{{ $cs }}">
                            @error('name')
                                <span style="font-size:12px;color:var(--couleur-danger)">{{ $message }}</span>
                            @enderror
                        </div>

                        <div style="display:flex;flex-direction:column;gap:5px">
                            <label style="font-size:12.5px;font-weight:600">Adresse e-mail *</label>
                            <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                                   style="{{ $cs }}">
                            @error('email')
                                <span style="font-size:12px;color:var(--couleur-danger)">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div style="display:flex;justify-content:flex-end;margin-top:18px">
                        <button type="submit" class="btn btn-principal">
                            <i class="bi bi-check-lg"></i>Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ═══════════ MOT DE PASSE ═══════════ --}}
        <div class="carte" id="mot-de-passe">
            <div class="corps-carte">
                <h2 style="font-size:15px;font-weight:700;margin-bottom:4px">Changer le mot de passe</h2>
                <p style="font-size:12.5px;color:var(--couleur-texte-secondaire);margin-bottom:18px">
                    Utilise un mot de passe long et unique pour protéger ton compte.
                </p>

                <form method="POST" action="{{ route('password.update') }}">
                    @csrf
                    @method('PUT')

                    <div style="display:flex;flex-direction:column;gap:14px">
                        <div style="display:flex;flex-direction:column;gap:5px">
                            <label style="font-size:12.5px;font-weight:600">Mot de passe actuel *</label>
                            <input type="password" name="current_password" autocomplete="current-password"
                                   style="{{ $cs }}">
                            @error('current_password', 'updatePassword')
                                <span style="font-size:12px;color:var(--couleur-danger)">{{ $message }}</span>
                            @enderror
                        </div>

                        <div style="display:flex;flex-direction:column;gap:5px">
                            <label style="font-size:12.5px;font-weight:600">Nouveau mot de passe *</label>
                            <input type="password" name="password" autocomplete="new-password"
                                   style="{{ $cs }}">
                            @error('password', 'updatePassword')
                                <span style="font-size:12px;color:var(--couleur-danger)">{{ $message }}</span>
                            @enderror
                        </div>

                        <div style="display:flex;flex-direction:column;gap:5px">
                            <label style="font-size:12.5px;font-weight:600">Confirmer le nouveau mot de passe *</label>
                            <input type="password" name="password_confirmation" autocomplete="new-password"
                                   style="{{ $cs }}">
                        </div>
                    </div>

                    <div style="display:flex;justify-content:flex-end;margin-top:18px">
                        <button type="submit" class="btn btn-principal">
                            <i class="bi bi-key-fill"></i>Mettre à jour le mot de passe
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ═══════════ AUTHENTIFICATION À DEUX FACTEURS ═══════════ --}}
        <div class="carte" id="deux-facteurs">
            <div class="corps-carte">
                <div style="display:flex;align-items:center;justify-content:space-between;gap:12px;margin-bottom:4px">
                    <h2 style="font-size:15px;font-weight:700">Authentification à deux facteurs</h2>
                    @if($user->possedeDeuxFacteursActifs())
                        <span style="background:#E7FBF3;color:#049566;font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;white-space:nowrap">
                            <i class="bi bi-shield-check"></i> Activée
                        </span>
                    @else
                        <span style="background:var(--couleur-fond);color:var(--couleur-texte-secondaire);font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px;white-space:nowrap">
                            Désactivée
                        </span>
                    @endif
                </div>
                <p style="font-size:12.5px;color:var(--couleur-texte-secondaire);margin-bottom:18px">
                    Ajoute une couche de sécurité supplémentaire : un code généré par une application
                    d'authentification (Google Authenticator, Microsoft Authenticator, Authy...) te sera
                    demandé en plus de ton mot de passe à chaque connexion.
                </p>

                @if(session('status') === 'two-factor-authentication-confirmed' || session('status') === 'two-factor-recovery-codes-regenerated')
                    {{-- Codes de récupération à sauvegarder (affichés une seule fois) --}}
                    <div style="background:#FEF5E0;border:1px solid #F7B731;border-radius:10px;padding:16px;margin-bottom:18px">
                        <div style="display:flex;align-items:center;gap:8px;font-weight:700;font-size:13px;color:#B07A00;margin-bottom:8px">
                            <i class="bi bi-exclamation-triangle-fill"></i> Codes de récupération
                        </div>
                        <p style="font-size:12.5px;color:#8A6200;margin-bottom:12px">
                            Conserve ces codes en lieu sûr. Chacun ne peut être utilisé qu'une seule fois pour
                            te connecter si tu perds l'accès à ton application d'authentification. Ils ne
                            seront plus jamais affichés.
                        </p>
                        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px;font-family:monospace;font-size:13px;background:#fff;border-radius:8px;padding:12px">
                            @foreach(session('codesRecuperation', []) as $code)
                                <div>{{ $code }}</div>
                            @endforeach
                        </div>
                    </div>
                @endif

                @if($user->possedeDeuxFacteursActifs())
                    {{-- 2FA active : proposer régénération des codes et désactivation --}}
                    <div style="display:flex;gap:10px;flex-wrap:wrap">
                        <form method="POST" action="{{ route('two-factor.recovery-codes') }}">
                            @csrf
                            <button type="submit" class="btn btn-contour">
                                <i class="bi bi-arrow-repeat"></i>Régénérer les codes de récupération
                            </button>
                        </form>

                        <button type="button" class="btn btn-danger" onclick="document.getElementById('modal-desactiver-2fa').style.display='flex'">
                            <i class="bi bi-shield-slash"></i>Désactiver la 2FA
                        </button>
                    </div>

                    {{-- Modale de désactivation (mot de passe requis) --}}
                    <div id="modal-desactiver-2fa" style="display:none;position:fixed;inset:0;z-index:9000;background:rgba(0,0,0,0.5);align-items:center;justify-content:center;padding:16px">
                        <div style="background:#fff;border-radius:14px;padding:26px;max-width:380px;width:100%;box-shadow:0 20px 60px rgba(0,0,0,0.25)">
                            <h3 style="font-size:16px;font-weight:700;margin-bottom:6px">Désactiver la 2FA</h3>
                            <p style="font-size:13px;color:var(--couleur-texte-secondaire);margin-bottom:16px">
                                Confirme ton mot de passe pour désactiver l'authentification à deux facteurs.
                            </p>
                            <form method="POST" action="{{ route('two-factor.disable') }}">
                                @csrf
                                @method('DELETE')
                                <input type="password" name="password" placeholder="Mot de passe actuel"
                                       style="{{ $cs }}" required>
                                @error('password', 'disableTwoFactorAuthentication')
                                    <span style="font-size:12px;color:var(--couleur-danger)">{{ $message }}</span>
                                @enderror
                                <div style="display:flex;gap:10px;margin-top:16px">
                                    <button type="button" class="btn btn-contour" style="flex:1;justify-content:center"
                                            onclick="document.getElementById('modal-desactiver-2fa').style.display='none'">
                                        Annuler
                                    </button>
                                    <button type="submit" class="btn btn-danger" style="flex:1;justify-content:center">
                                        Désactiver
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                @elseif($user->two_factor_secret && ! $user->two_factor_confirmed_at)
                    {{-- Activation en cours : scanner le QR code puis confirmer --}}
                    <div style="display:flex;gap:24px;flex-wrap:wrap;align-items:flex-start;margin-bottom:16px">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ urlencode((new \App\Support\TwoFactorAuthenticationProvider)->urlOtpAuth('UVCI - Gestion des heures', $user->email, $user->two_factor_secret)) }}"
                             alt="QR code d'authentification à deux facteurs"
                             style="border:1px solid var(--couleur-bordure);border-radius:10px;padding:8px;background:#fff">

                        <div style="flex:1;min-width:220px">
                            <p style="font-size:13px;margin-bottom:10px">
                                1. Scanne ce QR code avec ton application d'authentification.<br>
                                2. Saisis le code à 6 chiffres généré pour confirmer.
                            </p>

                            <form method="POST" action="{{ route('two-factor.confirm') }}" style="display:flex;gap:8px">
                                @csrf
                                <input type="text" name="code" inputmode="numeric" maxlength="6" placeholder="123456"
                                       style="{{ $cs }};max-width:130px;text-align:center;letter-spacing:3px;font-weight:700">
                                <button type="submit" class="btn btn-principal">
                                    <i class="bi bi-check-lg"></i>Confirmer
                                </button>
                            </form>
                            @error('code', 'confirmTwoFactorAuthentication')
                                <span style="font-size:12px;color:var(--couleur-danger);display:block;margin-top:6px">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                @else
                    {{-- 2FA non configurée --}}
                    <form method="POST" action="{{ route('two-factor.enable') }}">
                        @csrf
                        <button type="submit" class="btn btn-principal">
                            <i class="bi bi-shield-lock"></i>Activer la 2FA
                        </button>
                    </form>
                @endif
            </div>
        </div>

        {{-- ═══════════ SUPPRESSION DU COMPTE ═══════════ --}}
        <div class="carte" id="supprimer-compte" style="border-color:#FBD5DB">
            <div class="corps-carte">
                <h2 style="font-size:15px;font-weight:700;color:var(--couleur-danger);margin-bottom:4px">Supprimer le compte</h2>
                <p style="font-size:12.5px;color:var(--couleur-texte-secondaire);margin-bottom:18px">
                    Une fois ton compte supprimé, toutes ses données seront définitivement effacées.
                    Cette action est irréversible.
                </p>

                <button type="button" class="btn btn-danger" onclick="document.getElementById('modal-supprimer-compte').style.display='flex'">
                    <i class="bi bi-trash3"></i>Supprimer mon compte
                </button>

                <div id="modal-supprimer-compte" style="display:none;position:fixed;inset:0;z-index:9000;background:rgba(0,0,0,0.5);align-items:center;justify-content:center;padding:16px">
                    <div style="background:#fff;border-radius:14px;padding:26px;max-width:380px;width:100%;box-shadow:0 20px 60px rgba(0,0,0,0.25)">
                        <h3 style="font-size:16px;font-weight:700;margin-bottom:6px">Confirmer la suppression</h3>
                        <p style="font-size:13px;color:var(--couleur-texte-secondaire);margin-bottom:16px">
                            Confirme ton mot de passe. Cette action est définitive.
                        </p>
                        <form method="POST" action="{{ route('profile.destroy') }}">
                            @csrf
                            @method('DELETE')
                            <input type="password" name="password" placeholder="Mot de passe actuel"
                                   style="{{ $cs }}" required>
                            @error('password', 'userDeletion')
                                <span style="font-size:12px;color:var(--couleur-danger)">{{ $message }}</span>
                            @enderror
                            <div style="display:flex;gap:10px;margin-top:16px">
                                <button type="button" class="btn btn-contour" style="flex:1;justify-content:center"
                                        onclick="document.getElementById('modal-supprimer-compte').style.display='none'">
                                    Annuler
                                </button>
                                <button type="submit" class="btn btn-danger" style="flex:1;justify-content:center">
                                    Supprimer définitivement
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>

    @if(($errors->any() && !$errors->updatePassword && !$errors->confirmTwoFactorAuthentication && !$errors->disableTwoFactorAuthentication) && ($errors->has('name') || $errors->has('email')))
        <script>document.getElementById('informations')?.scrollIntoView({block:'center'});</script>
    @elseif($errors->updatePassword ?? false)
        <script>document.getElementById('mot-de-passe')?.scrollIntoView({block:'center'});</script>
    @elseif(($errors->confirmTwoFactorAuthentication ?? false) || ($errors->disableTwoFactorAuthentication ?? false) || request('deuxFacteurs'))
        <script>document.getElementById('deux-facteurs')?.scrollIntoView({block:'center'});</script>
    @endif

@endsection
