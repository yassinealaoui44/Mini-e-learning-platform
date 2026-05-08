<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocaleFromRequest
{
    private const SUPPORTED_LOCALES = ['en', 'fr'];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->resolveLocale($request);

        app()->setLocale($locale);

        return $next($request);
    }

    private function resolveLocale(Request $request): string
    {
        $requestedLocale = $request->header('X-Locale')
            ?? $request->cookie('academyhub_locale')
            ?? $request->getPreferredLanguage(self::SUPPORTED_LOCALES);

        $normalizedLocale = strtolower(substr((string) $requestedLocale, 0, 2));

        return in_array($normalizedLocale, self::SUPPORTED_LOCALES, true) ? $normalizedLocale : config('app.locale', 'en');
    }
}
