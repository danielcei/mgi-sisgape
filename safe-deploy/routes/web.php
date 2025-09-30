<?php

declare(strict_types=1);

use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use SafeDeploy\Constants;
use SafeDeploy\Laravel\Contracts\SetLocalePreference;

Route::middleware([
    EncryptCookies::class,
    AddQueuedCookiesToResponse::class,
    StartSession::class,
    AuthenticateSession::class,
    ShareErrorsFromSession::class,
    VerifyCsrfToken::class,
    SubstituteBindings::class,
])->group(function (): void {
    Route::post('/safe-deploy/set-locale', function (Request $request) {
        $locale = $request->input('locale');
        if (! array_key_exists($locale, config('app.locales'))) {
            abort(Response::HTTP_BAD_REQUEST);
        }

        if (Auth::check() && Auth::user() instanceof SetLocalePreference) {
            Auth::user()->setLocalePreference($locale);
        }

        return back()->cookie('locale', $locale, Constants::MONTH_IN_MINUTES);
    })->name('safe-deploy.set-locale');
});
