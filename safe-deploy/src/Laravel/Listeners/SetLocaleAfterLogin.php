<?php

declare(strict_types=1);

namespace SafeDeploy\Laravel\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cookie;
use SafeDeploy\Constants;

class SetLocaleAfterLogin
{
    public function handle(Login $event): void
    {
        $user = $event->user;
        /** @var ?string $locale */
        $locale = $user->locale
            ?? Cookie::get('locale')
            ?? config('app.locale');
        /** @var array<string, string> $locales */
        $locales = config('app.locales', []);

        if ($locale && array_key_exists($locale, $locales)) {
            App::setLocale($locale);
            Cookie::queue(Cookie::make('locale', $locale, Constants::MONTH_IN_MINUTES));
        }
    }
}
