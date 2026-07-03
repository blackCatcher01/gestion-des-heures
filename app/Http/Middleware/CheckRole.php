<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        // Utilisateur non connecté
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();

        // Compte désactivé
        if (!$user->actif) {
            auth()->logout();
            return redirect()->route('login')
                ->withErrors(['email' => 'Votre compte a été désactivé.']);
        }

        // Vérification du rôle
        if (!in_array($user->role, $roles)) {
            abort(403, 'Accès non autorisé pour votre rôle.');
        }

        return $next($request);
    }
}