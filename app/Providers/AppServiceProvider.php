<?php

namespace App\Providers;

use App\Models\User;
use App\Observers\UserObserver;
use App\View\Components\SimpleCustom;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

    }


    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        LanguageSwitch::configureUsing(function (LanguageSwitch $switch) {
            $switch
                ->locales(['pt_BR'])
                ->flags([
                    'pt_BR' => asset('flags/brazil.svg'),
                    //'en' => asset('flags/usa.svg'),
                ])
                ->circular();
        });
        User::observe(UserObserver::class);

        Builder::macro('toRawSql', function () {
            dd(vsprintf(str_replace(['?'], ['\'%s\''], $this->toSql()), $this->getBindings()));
        });
    }
}
