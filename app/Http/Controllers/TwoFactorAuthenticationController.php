<?php

namespace App\Http\Controllers;

use App\Support\TwoFactorAuthenticationProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class TwoFactorAuthenticationController extends Controller
{
    public function __construct(protected TwoFactorAuthenticationProvider $totp)
    {
    }

    /**
     * Démarre l'activation : génère un secret (non confirmé) et affiche le QR code.
     */
    public function store(Request $request): RedirectResponse
    {
        $utilisateur = $request->user();

        $utilisateur->forceFill([
            'two_factor_secret' => $this->totp->genererCleSecrete(),
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();

        return Redirect::route('profile.edit', ['deuxFacteurs' => 1])
            ->with('status', 'two-factor-authentication-started');
    }

    /**
     * Confirme l'activation en vérifiant le code TOTP saisi, puis génère les codes de secours.
     */
    public function confirm(Request $request): RedirectResponse
    {
        $request->validate([
            'code' => ['required', 'string'],
        ]);

        $utilisateur = $request->user();

        if (! $utilisateur->two_factor_secret || ! $this->totp->verifier($utilisateur->two_factor_secret, $request->input('code'))) {
            return back()
                ->withErrors(['code' => 'Le code saisi est incorrect ou a expiré. Réessaie avec le code actuel de ton application.'], 'confirmTwoFactorAuthentication')
                ->with('status', 'two-factor-authentication-started');
        }

        $codes = $this->totp->genererCodesRecuperation();

        $utilisateur->forceFill([
            'two_factor_recovery_codes' => $codes,
            'two_factor_confirmed_at' => now(),
        ])->save();

        return Redirect::route('profile.edit')->with([
            'status' => 'two-factor-authentication-confirmed',
            'codesRecuperation' => $codes,
        ]);
    }

    /**
     * Régénère un nouveau lot de codes de récupération (invalide les anciens).
     */
    public function regenererCodesRecuperation(Request $request): RedirectResponse
    {
        $utilisateur = $request->user();

        if (! $utilisateur->possedeDeuxFacteursActifs()) {
            return back();
        }

        $codes = $this->totp->genererCodesRecuperation();

        $utilisateur->forceFill(['two_factor_recovery_codes' => $codes])->save();

        return Redirect::route('profile.edit')->with([
            'status' => 'two-factor-recovery-codes-regenerated',
            'codesRecuperation' => $codes,
        ]);
    }

    /**
     * Désactive complètement la 2FA (mot de passe requis).
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('disableTwoFactorAuthentication', [
            'password' => ['required', 'current_password'],
        ]);

        $request->user()->forceFill([
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ])->save();

        return Redirect::route('profile.edit')->with('status', 'two-factor-authentication-disabled');
    }
}
