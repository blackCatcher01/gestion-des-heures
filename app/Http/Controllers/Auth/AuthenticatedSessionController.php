<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $utilisateur = Auth::user();

        // Si la 2FA est activée et confirmée sur ce compte, on ne termine pas la
        // connexion tout de suite : on la met en attente et on redirige vers l'étape
        // de vérification du code.
        if ($utilisateur->possedeDeuxFacteursActifs()) {
            $seSouvenirDeMoi = $request->boolean('remember');

            Auth::guard('web')->logout();

            $request->session()->regenerate();
            $request->session()->put('login.id', $utilisateur->id);
            $request->session()->put('login.remember', $seSouvenirDeMoi);

            return redirect()->route('two-factor.challenge');
        }

        $request->session()->regenerate();

        return redirect()->route('redirect');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
