<?php

declare(strict_types=1);

namespace SafeDeploy\Laravel\Concerns;

use Illuminate\Console\Command;
use Illuminate\Support\ServiceProvider;

/** @mixin Command|ServiceProvider */
trait PublishesVendorFiles
{
    /**
     * Copies vendor config and migration files to project
     *
     * @param  array<string>  $configFiles
     */
    protected function publishVendorFiles(array $configFiles, string $provider): int
    {
        $this->warn('Publishing vendor configs...');

        if (collect($configFiles)->some(static fn (string $file): bool => ! file_exists(config_path($file)))) {
            $this->call('vendor:publish', [
                '--provider' => $provider,
                '--tag' => 'config',
            ]);

            $this->warn('Cleaning config cache...');
            $this->call('config:clear');

            $this->warn('NOTE: Please call this command again in order to load the new config files and resume the process.');

            return Command::FAILURE;
        }

        $this->info('All good. No config missing!');

        $this->warn('Publishing migrations...');
        $this->call('vendor:publish', [
            '--provider' => $provider,
            '--tag' => 'migrations',
        ]);

        return Command::SUCCESS;
    }

    /**
     * Register publishable a batch of config files
     *
     * @param  array<string>  $configFiles
     */
    protected function registerConfigBatch(string $baseDir, array $configFiles): void
    {
        $configs = collect($configFiles)->mapWithKeys(static fn (string $file) => [
            "{$baseDir}/config/{$file}" => config_path($file),
        ])->toArray();
        $this->publishes($configs, 'config');
    }
}
