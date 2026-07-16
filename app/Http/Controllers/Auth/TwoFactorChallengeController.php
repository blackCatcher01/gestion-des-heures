<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Support\TwoFactorAuthenticationProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\View\View;

class TwoFactorChallengeController extends Controller
{
    /**
     * Affiche le formulaire de vérification du code, seulement si une connexion
     * est en attente de validation (session posée par AuthenticatedSessionController).
     */
    public function show(Request $request): View|RedirectResponse
    {
        if (! $request->session()->has('login.id')) {
            return redirect()->route('login');
        }

        return view('auth.two-factor-challenge');
    }

    /**
     * Vérifie le code TOTP saisi (ou un code de récupération) et termine la connexion.
     */
    public function store(Request $request, TwoFactorAuthenticationProvider $totp): RedirectResponse
    {
        $identifiantUtilisateur = $request->session()->get('login.id');

        if (! $identifiantUtilisateur) {
            return redirect()->route('login');
        }

        $cleThrottle = 'two-factor:'.$identifiantUtilisateur;

        if (RateLimiter::tooManyAttempts($cleThrottle, 5)) {
            $secondes = RateLimiter::availableIn($cleThrottle);

            return back()->withErrors([
                'code' => "Trop de tentatives. Réessaie dans {$secondes} secondes.",
            ]);
        }

        $request->validate([
            'code' => ['nullable', 'string'],
            'code_recuperation' => ['nullable', 'string'],
        ]);

        $utilisateur = User::find($identifiantUtilisateur);

        if (! $utilisateur) {
            return redirect()->route('login');
        }

        $valide = false;

        if ($request->filled('code') && $totp->verifier($utilisateur->two_factor_secret, $request->input('code'))) {
            $valide = true;
        } elseif ($request->filled('code_recuperation')) {
            $valide = $this->consommerCodeRecuperation($utilisateur, $request->input('code_recuperation'));
        }

        if (! $valide) {
            RateLimiter::hit($cleThrottle);

            return back()->withErrors([
                'code' => 'Le code saisi est invalide.',
            ]);
        }

        RateLimiter::clear($cleThrottle);

        $seSouvenirDeMoi = $request->session()->pull('login.remember', false);
        $request->session()->forget('login.id');

        Auth::login($utilisateur, $seSouvenirDeMoi);
        $request->session()->regenerate();

        return redirect()->route('redirect');
    }

    /**
     * Vérifie et invalide (usage unique) un code de récupération.
     */
    protected function consommerCodeRecuperation(User $utilisateur, string $codeSaisi): bool
    {
        $codes = $utilisateur->two_factor_recovery_codes ?? [];
        $codeSaisi = strtoupper(trim($codeSaisi));

        if (! in_array($codeSaisi, $codes, true)) {
            return false;
        }

        $utilisateur->forceFill([
            'two_factor_recovery_codes' => array_values(array_diff($codes, [$codeSaisi])),
        ])->save();

        return true;
    }
}
