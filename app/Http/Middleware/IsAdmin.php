<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
// 🚀 ADD THIS LINE BELOW:
use Illuminate\Support\Facades\Auth;
class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && \App\Models\Admin::where('id_utilisateur', Auth::id())->exists()) {
        return $next($request);
    }
    return redirect('/')->with('error', 'Accès réservé aux administrateurs.');
    }
}
