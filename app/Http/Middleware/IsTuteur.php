<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Tuteur;
use Symfony\Component\HttpFoundation\Response;

class IsTuteur
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is logged in AND exists in the tuteurs table
        if (auth()->check() && Tuteur::where('id_utilisateur', auth()->id())->exists()) {
            return $next($request);
        }

        // If not a tutor, send them to the student dashboard with an error
        return redirect()->route('etudiant.dashboard')->with('error', 'Accès réservé aux tuteurs.');
    }
}