<?php

declare(strict_types=1);

namespace App\Providers\Filament;

use Althinect\FilamentSpatieRolesPermissions\FilamentSpatieRolesPermissionsPlugin;
use App\Enums\AppNavigation;
use App\Filament\Pages\Auth\Login;
use App\Http\Middleware\CheckUser;
use App\Providers\Mps\MPSAvatarProvider;
use Filament\AvatarProviders\UiAvatarsProvider;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages\Page;
use Filament\Panel;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\Alignment;
use Filament\Support\Enums\MaxWidth;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Contracts\View\View;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Collection;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Jeffgreco13\FilamentBreezy\BreezyCore;
use Override;
use SafeDeploy\Filament\Providers\SafeDeployPanelProvider;
use Filament\Widgets;

class MPSFilamentProvider extends SafeDeployPanelProvider
{
    protected string $name = 'Mps';

    #[Override]
    public function boot(): void
    {
        parent::boot();

        $this->app->singleton(UiAvatarsProvider::class, MPSAvatarProvider::class);

        Page::formActionsAlignment(Alignment::Right);
        Page::stickyFormActions();

        FilamentView::registerRenderHook(
            PanelsRenderHook::TOPBAR_START,
            fn(): View => view('mps.sidebar-collapsed-logo'),
        );

        FilamentView::registerRenderHook(
            PanelsRenderHook::FOOTER,
            fn(): View => view('mps.footer'),
        );

    }

    protected function setupBrandingOn(Panel $panel): void
    {
        $panel
            ->default()
            ->path('admin')
            ->login(Login::class)
            ->passwordReset()
            ->colors([
                'danger' => Color::Rose,
                'info' => Color::Blue,
                'success' => '#019b55',
                'warning' => Color::Orange,
                'primary' => '#183EFF',
                'gray' => '#294d92',
                ...$this->getNavigationColors(),
            ])
            ->brandLogo(asset('images/logo.png'))
            ->darkModeBrandLogo(asset('images/logo_branca.png'))
            ->brandLogoHeight('3rem')
            ->favicon(asset('images/favicon.png'))
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                CheckUser::class,
            ])
            ->plugin(
                BreezyCore::make()
                    ->myProfile(
                        shouldRegisterUserMenu: true,
                        shouldRegisterNavigation: false,
                        //navigationGroup: 'Settings',
                        hasAvatars: true,
                        slug: 'my-profile'
                    )
                    ->enableTwoFactorAuthentication(
                        force: false
                    )
            )
            ->plugin(FilamentSpatieRolesPermissionsPlugin::make())
            ->viteTheme('resources/css/filament/mps/theme.scss')
            ->maxContentWidth(MaxWidth::Full)
            ->font('IBM Plex Sans');
    }

    protected function setupNavigationOn(Panel $panel): void
    {
        $panel
            ->databaseNotifications()
            ->sidebarCollapsibleOnDesktop()
            ->navigationGroups(
                Collection::make(AppNavigation::cases())
                    ->map(fn(AppNavigation $navigation): NavigationGroup => NavigationGroup::make($navigation->getLabel())
                        ->icon(fn(): ?string => $navigation->getIcon())
                        ->extraSidebarAttributes(['class' => $navigation->value])
                        ->collapsed($navigation->isCollapsedByDefault()))->all()
            );
    }

    protected function setupPagesOn(Panel $panel): void
    {
        $panel
            ->pages([
            ]);
    }

    /**
     * @return array<string, string>
     */
    private function getNavigationColors(): array
    {
        return Collection::make(AppNavigation::cases())
            ->mapWithKeys(fn(AppNavigation $nav): array => [
                $nav->getColor() => $nav->getColorHex(),
            ])
            ->all();
    }
}
