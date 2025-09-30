<?php

declare(strict_types=1);

namespace SafeDeploy\Filament\Providers;

use Closure;
use Exception;
use Filament\Forms;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Component as FormComponent;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Infolists\Components\TextEntry;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Enums\MaxWidth;
use Filament\Support\Enums\Platform;
use Filament\Support\Facades\FilamentView;
use Filament\Support\View\Components\Modal;
use Filament\View\PanelsRenderHook;
use Illuminate\Contracts\View\View;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Str;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use SafeDeploy\Filament\Icons\PhosphorIcons;
use SafeDeploy\Laravel\Http\Middleware\SetLocale;

class SafeDeployPanelProvider extends PanelProvider
{
    protected string $name = 'SafeDeploy';

    public function boot(): void
    {
        $this->registerMacros();
        $this->preventModalAccidentalClosure();
        $this->registerViewHooks();
    }

    /**
     * @throws Exception
     */
    public function panel(Panel $panel): Panel
    {
        $this->setupDefaultsOn($panel, $this->name);
        $this->setupBrandingOn($panel);
        $this->setupNavigationOn($panel);
        $this->setupPagesOn($panel);
        $this->setupPluginsOn($panel);
        $this->setupWidgetsOn($panel);
        $this->setupDiscoveriesOn($panel, $this->name);
        $this->setupGlobalSearchOn($panel);
        $this->setupMiddlewareOn($panel);

        return $panel;
    }

    protected function preventModalAccidentalClosure(): void
    {
        Modal::closedByClickingAway(false);
    }

    protected function registerMacros(): void
    {
        FormComponent::macro(
            'showWhen',
            function (string $field, string $value): FormComponent {
                $showWhen = fn (FormComponent $component, string $field, string $value): FormComponent => $component
                    ->hidden(fn (Forms\Get $get): bool => $get($field) !== $value);

                return Closure::bind(
                    fn (): Component => $showWhen($this, $field, $value),
                    $this,
                    FormComponent::class
                )();
            }
        );

        TextEntry::macro(
            'showStateAsToolTip',
            function (): TextEntry {
                $showStateAsToolTip = fn (TextEntry $component): TextEntry => $component
                    ->tooltip(fn (?string $state): ?string => $state);

                return Closure::bind(fn (): TextEntry => $showStateAsToolTip($this), $this, TextEntry::class)();
            }
        );

        TextEntry::macro(
            'showToolTipIfLimited',
            function (): TextEntry {
                $showToolTipIfLimited = fn (TextEntry $component): TextEntry => $component
                    ->tooltip(fn (?string $state, TextEntry $component): ?string => $state
                    && mb_strlen($state) > $component->getCharacterLimit() ? $state : null);

                return Closure::bind(fn (): TextEntry => $showToolTipIfLimited($this), $this, TextEntry::class)();
            }
        );
    }

    protected function registerViewHooks(): void
    {
        FilamentView::registerRenderHook(
            PanelsRenderHook::USER_MENU_PROFILE_AFTER,
            fn (): View => view('safe-deploy::filament.user-menu.locale-selector'),
        );
    }

    protected function setupBrandingOn(Panel $panel): void {}

    /**
     * @throws Exception
     */
    protected function setupDefaultsOn(Panel $panel, string $panelName): void
    {
        $panel
            ->default()
            ->id(Str::lower($panelName))
            ->path('/')
            ->spa()
            ->unsavedChangesAlerts()
            ->databaseTransactions()
            ->readOnlyRelationManagersOnResourceViewPagesByDefault(false)
            ->login()
            ->profile()
            ->maxContentWidth(MaxWidth::Full);
    }

    protected function setupDiscoveriesOn(Panel $panel, string $panelName): void
    {
        $panel
            ->discoverResources(
                in: app_path("Filament/{$panelName}/Resources"),
                for: "App\\Filament\\{$panelName}\\Resources"
            )
            ->discoverPages(
                in: app_path("Filament/{$panelName}/Pages"),
                for: "App\\Filament\\{$panelName}\\Pages"
            )
            ->discoverClusters(
                in: app_path("Filament/{$panelName}/Clusters"),
                for: "App\\Filament\\{$panelName}\\Clusters"
            )
            ->discoverWidgets(
                in: app_path("Filament/{$panelName}/Widgets"),
                for: "App\\Filament\\{$panelName}\\Widgets"
            );
    }

    protected function setupGlobalSearchOn(Panel $panel): void
    {
        $panel
            ->globalSearchKeyBindings(['command+k', 'ctrl+k'])
            ->globalSearchFieldSuffix(fn (): ?string => match (Platform::detect()) {
                Platform::Windows, Platform::Linux => 'CTRL+K',
                Platform::Mac => '⌘K',
                default => null,
            });
    }

    protected function setupMiddlewareOn(Panel $panel): void
    {
        $panel
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                SetLocale::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }

    protected function setupNavigationOn(Panel $panel): void {}

    protected function setupPagesOn(Panel $panel): void {}

    protected function setupPluginsOn(Panel $panel): void
    {
        $panel
            ->plugins([
                PhosphorIcons::make()->light(),
            ]);
    }

    protected function setupWidgetsOn(Panel $panel): void {}
}
