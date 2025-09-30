<?php

declare(strict_types=1);

namespace SafeDeploy\Laravel\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class SetLocale
{
    public function handle(Request $request, Closure $next): mixed
    {
        /** @var ?string $locale */
        $locale = data_get(Auth::user(), 'locale', Cookie::get('locale'));
        /** @var array<string, string> $locales */
        $locales = config('app.locales', []);

        if ($locale && array_key_exists($locale, $locales)) {
            App::setLocale($locale);
        }

        return $next($request);
    }
}
