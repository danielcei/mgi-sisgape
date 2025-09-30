<?php

declare(strict_types=1);

namespace SafeDeploy;

use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Events\PublishingStubs;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Override;
use SafeDeploy\Laravel\Commands\SafeDeployInstall;
use SafeDeploy\Laravel\Concerns\PublishesVendorFiles;
use SafeDeploy\Laravel\Helpers\DBHelper;
use SafeDeploy\Laravel\Listeners\ExtendLaravelStubs;

/**
 * @mixin Command The provider acts as a Command when running artisan publish, etc.
 */
class SafeDeployServiceProvider extends ServiceProvider
{
    use PublishesVendorFiles;

    /**
     * @var array<string> Files that need to be published into app config (from vendor packages or not)
     */
    public static array $configFiles = [
        'safe-deploy.php',
    ];

    /**
     * @var array<string> Service providers needed in DEV environment
     */
    public static $devServiceProviders = [
        // IDE Helper: https://github.com/barryvdh/laravel-ide-helper
        //        IdeHelperServiceProvider::class,
        // Translatable String Exporter:  https://github.com/kkomelin/laravel-translatable-string-exporter
        //        ExporterServiceProvider::class,
        // DotEnv Editor:  https://github.com/JackieDo/Laravel-Dotenv-Editor
        //        DotenvEditorServiceProvider::class
    ];

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->bootMigrations();
        $this->bootListeners();
        $this->bootViews();
        $this->bootRoutes();
    }

    /**
     * Register any application services.
     */
    #[Override]
    public function register(): void
    {
        parent::register();

        $runningInConsole = $this->app->runningInConsole();
        $notInProduction = $this->app->environment() !== 'production';
        $dotEnvDoesNotExists = ! file_exists(base_path('.env'));

        $this->extendExceptionHandlerWithSentry();
        $this->registerDatabaseBlueprintHooks();

        // General Commands
        if ($runningInConsole) {
            $this->registerGeneralCommands();
        }

        // Register services, assets, commands, etc for console in non production environments
        if (($dotEnvDoesNotExists || $notInProduction) && $runningInConsole) {
            $this->registerPublishableAssets();

            $this->registerDevProviders();

            $this->registerDevCommands();
        }
    }

    /**
     * Register web routes
     */
    protected function bootRoutes(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
    }

    /**
     * Sentry error reporting without the need to edit /app/Exceptions/Handler.php
     * We extend the default singleton with a decorator that is responsible for
     * exception handling with our own decorated class.
     */
    protected function extendExceptionHandlerWithSentry(): void
    {
        //        $this->app->extend(ExceptionHandler::class, static function ($service) {
        //            return new Exceptions\SentryHandler($service);
        //        });
    }

    protected function registerDatabaseBlueprintHooks(): void
    {
        Blueprint::macro('common', function (bool $withSoftDeletes = false, ?string $createdByColumn = null, ?string $updatedByColumn = null, ?string $deletedByColumn = null): void {
            /** @var Blueprint $this */
            DBHelper::addCommonColumns(
                $this,
                $withSoftDeletes,
                createdByColumn: $createdByColumn,
                updatedByColumn: $updatedByColumn,
                deletedByColumn: $deletedByColumn
            );
        });

        Blueprint::macro('addUserStamps', function (bool $softDeletes = true, bool $createsForeignKeys = true, ?string $usersTable = null, ?string $createdByColumn = null, ?string $updatedByColumn = null, ?string $deletedByColumn = null): void {
            /** @var Blueprint $this */
            DBHelper::addUserStampsTo(
                $this,
                $softDeletes,
                $createsForeignKeys,
                $usersTable,
                $createdByColumn,
                $updatedByColumn,
                $deletedByColumn
            );
        });

        Blueprint::macro('dropUserStamps', function (bool $softDeletes = true, bool $dropsForeignKeys = true, ?string $createdByColumn = null, ?string $updatedByColumn = null, ?string $deletedByColumn = null): void {
            /** @var Blueprint $this */
            DBHelper::dropUserStampsFrom(
                $this,
                $softDeletes,
                $dropsForeignKeys,
                $createdByColumn,
                $updatedByColumn,
                $deletedByColumn
            );
        });
    }

    /**
     * Register DEV commands
     */
    protected function registerDevCommands(): void
    {
        $this->commands([
            SafeDeployInstall::class,
        ]);
    }

    /**
     * Register DEV Service Providers
     */
    protected function registerDevProviders(): void
    {
        foreach (static::$devServiceProviders as $devServiceProvider) {
            $this->app->register($devServiceProvider);
        }
    }

    /**
     * Register general commands
     */
    protected function registerGeneralCommands(): void
    {
        $this->commands([
            // Prod commands
        ]);
    }

    /**
     * Register publishable assets
     */
    protected function registerPublishableAssets(): void
    {
        $this->registerConfigBatch(dirname(__DIR__), static::$configFiles);
    }

    private function bootListeners(): void
    {
        Event::listen(
            PublishingStubs::class,
            ExtendLaravelStubs::class,
        );
    }

    private function bootMigrations(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
    }

    private function bootViews(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'safe-deploy');
    }
}
