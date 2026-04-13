<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Etudiant;
use Symfony\Component\HttpFoundation\Response;

class IsEtudiant
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is logged in AND exists in the etudiants table
        if (auth()->check() && Etudiant::where('id_utilisateur', auth()->id())->exists()) {
            return $next($request);
        }

        // If not a student (meaning they are likely a tutor), send them to the tutor dashboard
        return redirect()->route('tuteur.dashboard')->with('error', 'Accès réservé aux étudiants.');
    }
}