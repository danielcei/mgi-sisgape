<?php

declare(strict_types=1);

namespace SafeDeploy;

use Filament\Facades\Filament;
use Filament\Resources\Resource;
use FilesystemIterator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Str;
use InvalidArgumentException;
use SafeDeploy\Laravel\Exceptions\DefaultUserModelNotFound;
use SafeDeploy\Laravel\Exceptions\MigrationsConnectionNotFound;
use SafeDeploy\Laravel\Exceptions\SafeDeployConfigMisconfiguration;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use ReflectionClass;
use ReflectionException;
use SplFileInfo;
use Throwable;

final class SafeDeploy
{
    /**
     * @return class-string<Model>
     *
     * @throws DefaultUserModelNotFound|SafeDeployConfigMisconfiguration|Throwable
     */
    public static function defaultUserModel(): string
    {
        /** @var class-string<Model>|string|null $userModel */
        $userModel = config('safe-deploy.default_user_model');

        throw_if($userModel === null, DefaultUserModelNotFound::class);
        throw_unless(is_subclass_of($userModel, Model::class), SafeDeployConfigMisconfiguration::class);

        return $userModel;
    }

    /**
     * @throws DefaultUserModelNotFound|SafeDeployConfigMisconfiguration|Throwable
     */
    public static function defaultUserTable(): string
    {
        $modelClass = self::defaultUserModel();

        return (new $modelClass)->getTable();
    }

    /**
     * @return ?class-string<resource>
     */
    public static function filamentResourceForClass(string $class): ?string
    {
        $modelClass = str_contains($class, '\\') ? $class : self::modelNamespace($class);

        if (! class_exists($modelClass)) {
            return null;
        }

        /** @var ?class-string<resource> $resourceClass */
        $resourceClass = Filament::getModelResource($modelClass);

        if ($resourceClass === null || ! class_exists($resourceClass)) {
            return null;
        }

        return $resourceClass;
    }

    public static function getNamespaceFromFile(string $file): ?string
    {
        if (! file_exists($file)) {
            throw new InvalidArgumentException("File {$file} not found.");
        }

        $fileContents = file_get_contents($file);

        if (! is_string($fileContents)) {
            return null;
        }

        if (preg_match('/namespace\s+([^;]+);/', $fileContents, $matches)) {
            return trim($matches[1]);
        }

        return null;
    }

    public static function localizedDate(): string
    {
        $key = 'safe-deploy::formats.date_format';

        return self::localizedFormatter($key, 'Y-m-d');
    }

    public static function localizedDateTime(): string
    {
        $key = 'safe-deploy::formats.datetime_format';

        return self::localizedFormatter($key, 'Y-m-d H:i:s');
    }

    /**
     * @throws MigrationsConnectionNotFound|SafeDeployConfigMisconfiguration
     * @throws Throwable
     */
    public static function migrationsConnection(): string
    {
        $connection = config('safe-deploy.migrations_connection');

        throw_if($connection === null, MigrationsConnectionNotFound::class);
        throw_unless(is_string($connection), SafeDeployConfigMisconfiguration::class);

        return $connection;
    }

    public static function modelNamespace(string $classBasename): string
    {
        /** @var string $namespace */
        $namespace = Config::get('safe-deploy.models.namespace', 'App\\Models\\');

        return "{$namespace}{$classBasename}";
    }

    /**
     * @return class-string<Model>[]
     */
    public static function modelsIn(string $dir): array
    {
        return self::getExistingClasses($dir, Model::class);
    }

    public static function path(string $path = ''): string
    {
        $scriptDir = __DIR__;
        $walkBackToRoot = '/..';
        $path = Str::start($path, '/');

        return self::getAbsolutePath("{$scriptDir}$walkBackToRoot{$path}");
    }

    /**
     * @return class-string<resource>[]
     */
    public static function resourcesIn(string $dir): array
    {
        return self::getExistingClasses($dir, Resource::class);
    }

    private static function getAbsolutePath(string $path): string
    {
        $path = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
        $parts = array_filter(explode(DIRECTORY_SEPARATOR, $path), static fn (string $part): bool => strlen($part) > 0);
        $absolutes = [];

        foreach ($parts as $part) {
            if ($part === '.') {
                continue;
            }

            if ($part === '..') {
                array_pop($absolutes);

                continue;
            }

            $absolutes[] = $part;
        }

        return implode(DIRECTORY_SEPARATOR, $absolutes);
    }

    /**
     * @template T of object
     *
     * @param  class-string<T>  $baseClass
     * @return class-string<T>[]
     */
    private static function getExistingClasses(string $dir, string $baseClass): array
    {
        /** @var array<SplFileInfo> $files */
        $files = array_values(iterator_to_array(new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS)
        )));

        $phpFiles = array_filter($files, fn ($file): bool => $file->isFile() && $file->getExtension() === 'php');

        $classes = array_map(
            fn (SplFileInfo $file): string => self::getNamespaceFromFile($file->getPathname()).'\\'
                .str_replace('.php', '', $file->getFilename()),
            $phpFiles
        );

        $existingClasses = array_filter($classes, fn (string $class): bool => class_exists($class));

        /**
         * @var array<int, class-string<T>> $filesList
         */
        $filesList = array_filter($existingClasses, function (string $class) use ($baseClass): bool {
            try {
                $ref = new ReflectionClass($class);

                return ! $ref->isAbstract() && $ref->isSubclassOf($baseClass);
            } catch (ReflectionException) {
                return false;
            }
        });

        return array_values($filesList);
    }

    private static function localizedFormatter(string $key, string $default): string
    {
        $format = __($key);

        return $format === $key ? $default : $format;
    }
}
