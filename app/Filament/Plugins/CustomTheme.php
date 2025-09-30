<?php

namespace Mvenghaus\FilamentThemeOsLight;

namespace App\Filament\Plugins;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Facades\FilamentView;
use Illuminate\Contracts\View\View;

class CustomTheme implements Plugin
{
    public function getId(): string
    {
        return 'custom-theme7';
    }

    public function register(Panel $panel): void
    {
        $this->registerLogoInTopbar($panel);
    }

    public function boot(Panel $panel): void
    {
    }

    public static function make(): static
    {
        return app(static::class);
    }

    private function registerLogoInTopbar(Panel $panel): void
    {
        FilamentView::registerRenderHook(
            'panels::topbar.end',
            fn(): View => view('custom-theme.topbar-logo', [
                'isSidebarCollapsibleOnDesktop' => $panel->isSidebarCollapsibleOnDesktop(),
            ])
        );
    }
}
