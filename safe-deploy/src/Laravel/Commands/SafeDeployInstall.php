<?php

declare(strict_types=1);

namespace SafeDeploy\Laravel\Commands;

use FilesystemIterator;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use OwenIt\Auditing\AuditingServiceProvider;
use SafeDeploy\SafeDeploy;
use SafeDeploy\Tools\EnvHelper;
use SafeDeploy\Tools\InteractsWithDotEnv;
use RuntimeException;
use Throwable;

class SafeDeployInstall extends Command
{
    use InteractsWithDotEnv;

    const int MKDIR_MODE = 0755;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Installs or updates SafeDeploy and helps to setup the Laravel project. It is not destructive, so you can run it as many times as you want to add features.';

    /**
     * The path to the package in the project
     */
    protected string $packagePath = '/vendor/safe-deploy/safe-deploy';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'safe-deploy:install';

    /**
     * Execute the console command.
     *
     * @throws Throwable
     */
    public function handle(): void
    {
        $this->copyEnvExample();

        $this->shouldGenerateAppKey();

        $this->shouldFillEnvWithSafeDeployConfig();

        $this->publishConfigFile();

        $this->shouldCopyStubs();

        $this->shouldMigrateUserStamps();

        $this->info('SafeDeploy installation completed successfully!');
    }

    /**
     * Copies $file.example to $file if it does no exists yet.
     * If it exists, tries to append the missing keys from $file.example
     *
     * @throws Throwable
     */
    protected function copyEnvExample(string $file = '.env'): void
    {
        $this->warn("Copying {$file}.example to {$file}...");

        $dotExamplePath = base_path("{$file}.example");
        $filePath = base_path($file);

        if (! file_exists($dotExamplePath)) {
            $this->warn("{$file}.example does not exists in the project! Copying from SafeDeploy package... ");
            $packageDotExamplePath = base_path("$this->packagePath/stubs/laravel/{$file}.example");
            throw_if(! copy($packageDotExamplePath, $dotExamplePath),
                new RuntimeException("Failed to copy package {$file}.example into project!"));
        }

        if (file_exists($filePath)) {
            $this->info("{$file} file already exists!");

            $this->copyMissingKeys($file, "{$file}.example");
        }

        if (! file_exists($filePath)) {
            throw_if(! copy($dotExamplePath, $filePath),
                new RuntimeException("Failed to copy {$file}.example to {$file}!"));
        }
    }

    protected function copyFiles(string $fromPath, string $toPath): void
    {
        $files = new FilesystemIterator($fromPath);

        foreach ($files as $file) {
            /** @var FilesystemIterator $file */
            if ($file->isFile()) {
                $fileName = $file->getFilename();
                $destPath = $toPath.'/'.$fileName;

                if (file_exists($destPath) && ! $this->confirm("The file '{$fileName}' already exists. Do you want to overwrite it?")) {
                    $this->info("Skipping '{$fileName}'");

                    continue;
                }

                $copyResult = copy($file->getPathname(), $destPath);

                if ($copyResult) {
                    $this->info("Copied: {$fileName}");
                }

                /*
                $contents = File::get($destPath);

                $updatedContents = preg_replace(
                    '/namespace\s+.*?;/',
                    "namespace $toPath;",
                    $contents
                );

                File::put($destPath, $updatedContents);
                */

                if (! $copyResult) {
                    $this->error("Failed to copy: {$fileName}");
                }
            }
        }
    }

    /**
     * Copy custom stubs from package to Laravel project's stubs directory
     */
    protected function copyStubs(): void
    {
        $this->info('Copying custom stubs to project...');

        $stubsPath = SafeDeploy::path('stubs/laravel/stubs');

        $laravelStubsPath = base_path('stubs');

        if (! is_dir($stubsPath)) {
            $this->error('Package stubs directory not found.');

            return;
        }

        if (! is_dir($laravelStubsPath)) {
            $this->info('Creating stubs directory in project root...');
            mkdir($laravelStubsPath, self::MKDIR_MODE, true);
        }

        $files = new FilesystemIterator($stubsPath);
        $filesCopied = 0;

        foreach ($files as $file) {
            /** @var FilesystemIterator $file */
            if ($file->isFile()) {
                $stubFileName = $file->getFilename();
                $destPath = $laravelStubsPath.'/'.$stubFileName;

                if (file_exists($destPath) && ! $this->confirm("The stub file '{$stubFileName}' already exists. Do you want to overwrite it?")) {
                    $this->info("Skipping '{$stubFileName}'");

                    continue;
                }

                $copyResult = copy($file->getPathname(), $destPath);

                if ($copyResult) {
                    $filesCopied++;
                    $this->info("Copied: {$stubFileName}");
                }

                if (! $copyResult) {
                    $this->error("Failed to copy: {$stubFileName}");
                }
            }
        }

        if ($filesCopied > 0) {
            $this->info("{$filesCopied} stub files were copied successfully.");
        }

        if ($filesCopied === 0) {
            $this->info('No stub files were copied.');
        }
    }

    protected function publishConfigFile(): void
    {
        $safeDeployConfigPath = SafeDeploy::path('config/');

        $laravelConfigPath = base_path('config');

        $files = new FilesystemIterator($safeDeployConfigPath);

        foreach ($files as $file) {
            /** @var FilesystemIterator $file */
            if ($file->isFile()) {
                $configFileName = $file->getFilename();
                $destPath = $laravelConfigPath.'/'.$configFileName;

                if (file_exists($destPath) && ! $this->confirm("The Safe Deploy config file '{$configFileName}' already exists. Do you want to overwrite it?")) {
                    $this->info("Skipping '{$configFileName}'");

                    continue;
                }

                $copyResult = copy($file->getPathname(), $destPath);

                if ($copyResult) {
                    $this->info("Copied: {$configFileName}");
                }

                if (! $copyResult) {
                    $this->error("Failed to copy: {$configFileName}");
                }
            }
        }

        $this->call('vendor:publish', ['--tag' => 'permission-config']);
    }

    protected function setSafeDeployDefaultUserModel(EnvHelper $dotEnv): void
    {
        $safeDeployDefaultUserModel = 'SAFE_DEPLOY_DEFAULT_USER_MODEL';
        $dotEnvValue = $dotEnv->getKey($safeDeployDefaultUserModel);
        if (blank($dotEnvValue)) {
            /** @var string $value */
            $value = $this->ask($safeDeployDefaultUserModel, 'App\Models\User');

            while (true) {
                /** @var string $value */
                if (class_exists($value)) {
                    break;
                }

                $this->error('Class does not exists!');

                $value = $this->ask($safeDeployDefaultUserModel, 'App\Models\User');
            }

            /** @var string $value */
            $dotEnv->setKey($safeDeployDefaultUserModel, $value);
        }
    }

    protected function setSafeDeployExtraEnvKeys(EnvHelper $dotEnv): void
    {
        $safeDeployConfig = [
            'SAFE_DEPLOY_USER_STAMP_CREATED_BY_COLUMN' => 'created_by',
            'SAFE_DEPLOY_USER_STAMP_UPDATED_BY_COLUMN' => 'updated_by',
            'SAFE_DEPLOY_USER_STAMP_DELETED_BY_COLUMN' => 'deleted_by',
        ];

        /**
         * @var string $envKey
         * @var string $envValue
         */
        foreach ($safeDeployConfig as $envKey => $envValue) {
            $dotEnvValue = $dotEnv->getKey($envKey);
            if (blank($dotEnvValue)) {
                /** @var string $value */
                $value = $this->ask($envKey, $envValue);

                $dotEnv->setKey($envKey, $value);
            }
        }
    }

    protected function setSafeDeployMigrationConnection(EnvHelper $dotEnv): void
    {
        $safeDeployDefaultConnection = 'SAFE_DEPLOY_MIGRATIONS_CONNECTION';
        $dotEnvValue = $dotEnv->getKey($safeDeployDefaultConnection);
        if (blank($dotEnvValue)) {
            /** @var string $value */
            $value = $this->ask($safeDeployDefaultConnection, 'non_persistent');

            while (true) {
                /** @var string $value */
                if (Arr::exists((array) config('database.connections'), $value)) {
                    break;
                }

                $this->error('Connection does not exists!');

                $value = $this->ask($safeDeployDefaultConnection, 'non_persistent');
            }

            /** @var string $value */
            $dotEnv->setKey($safeDeployDefaultConnection, $value);
        }
    }

    protected function shouldCopyStubs(): void
    {
        if ($this->confirm('Do you wish to copy the stubs files?')) {
            $this->copyStubs();
        }

        if ($this->confirm('Do you wish to copy the base model files?')) {
            $safeDeployModelPath = SafeDeploy::path('stubs/laravel/app/models');

            /** @var string $laravelModelPath */
            $laravelModelPath = $this->ask('Please, provide the models folder', 'App/Models');

            if (! is_dir($laravelModelPath)) {
                $this->error('Folder does not exists!');
                if ($this->confirm('Do you wish to create the models folder?')) {
                    mkdir($laravelModelPath, self::MKDIR_MODE, true);
                }
            }

            if (is_dir($laravelModelPath)) {
                $this->copyFiles($safeDeployModelPath, $laravelModelPath);
            }
        }

        if ($this->confirm('Do you wish to copy the BasePolicy file?')) {
            $safeDeployPoliciesPath = SafeDeploy::path('stubs/laravel/app/policies');

            /** @var string $laravelPolicyPath */
            $laravelPolicyPath = $this->ask('Please, provide que policy folder (be careful, the folder name is case sensitive)', 'app/Policies');

            if (! is_dir($laravelPolicyPath)) {
                $this->error('Folder does not exists!');

                if ($this->confirm('Do you wish to create the policy folder?')) {
                    mkdir($laravelPolicyPath, self::MKDIR_MODE, true);
                }
            }

            if (is_dir($laravelPolicyPath)) {
                $this->copyFiles($safeDeployPoliciesPath, $laravelPolicyPath);
            }
        }
    }

    protected function shouldFillEnvWithSafeDeployConfig(): void
    {
        if ($this->confirm('Do you wish to copy SafeDeploy config info to .env file?')) {
            $dotEnv = $this->getDotEnv('.env');

            $this->setSafeDeployDefaultUserModel($dotEnv);

            $this->setSafeDeployMigrationConnection($dotEnv);

            $this->setSafeDeployExtraEnvKeys($dotEnv);

            $this->info('Safe Deploy config info copied to .env file.');
        }
    }

    /**
     * Check for app key in .env. and ask if the user wants to replace it if already exists, otherwise, creates it.
     *
     * @throws Throwable
     */
    protected function shouldGenerateAppKey(): void
    {
        if ($this->confirm('Do you wish to generate app key?')) {
            $appKey = $this->getDotEnv('.env')->getKey('APP_KEY');

            if ($appKey === null || $appKey === '' || $appKey === '0') {
                $this->warn('Generating app key...');
                $this->call('key:generate');

                return;
            }

            if ($this->confirm('App key already exists in .env. Do you wish to reset it?')) {
                $this->call('key:generate');
            }
        }
    }

    protected function shouldMigrateUserStamps(): void
    {
        if ($this->confirm('Do you wish to migrate user stamps?')) {
            $this->info('Migrating user stamps...');
            $this->call('migrate', ['--path' => 'vendor/safe-deploy/safe-deploy/src/database/migrations']);
        }

        $this->call('vendor:publish', [
            '--provider' => AuditingServiceProvider::class,
            '--tag' => 'config',
        ]);

        $this->call('vendor:publish', [
            '--provider' => AuditingServiceProvider::class,
            '--tag' => 'migrations',
        ]);
    }
}
