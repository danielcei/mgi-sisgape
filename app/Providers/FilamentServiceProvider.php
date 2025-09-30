<?php

namespace App\Providers;

use Filament\FilamentServiceProvider as BaseServiceProvider;
use Illuminate\Support\Facades\Route;

class FilamentServiceProvider extends BaseServiceProvider
{
    public function boot()
    {
        parent::boot();

        Route::middleware('auth')
            ->group(function () {
                Route::get('/admin', function () {
                    return redirect()->route('dashboard');
                })->name('admin');
            });
    }
}
