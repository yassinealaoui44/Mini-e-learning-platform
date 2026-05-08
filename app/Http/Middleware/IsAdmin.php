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

        if ($request->expectsJson()) {
            return response()->json([
                'message' => __('portal.admin_only'),
            ], 403);
        }

        return redirect()->route('dashboard')->with('error', __('portal.admin_only_redirect'));
    }
}
