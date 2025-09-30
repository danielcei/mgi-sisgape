<?php

declare(strict_types=1);

use SafeDeploy\Laravel\Exceptions\DefaultUserModelNotFound;
use SafeDeploy\Laravel\Exceptions\MigrationsConnectionNotFound;
use SafeDeploy\Laravel\Exceptions\SafeDeployConfigMisconfiguration;
use SafeDeploy\SafeDeploy;

uses()->group('SafeDeployConfig');

it('throws exception when SafeDeploy is misconfigured', function (
    string $configKey,
    ?int $configValue,
    string $exceptionClass,
    callable $method
): void {
    $originalConfig = Config::get("safe-deploy.{$configKey}");

    Config::set("safe-deploy.{$configKey}", $configValue);

    $this->expectException($exceptionClass);

    $method();

    Config::set("safe-deploy.{$configKey}", $originalConfig);
})
    ->with([
        'empty user model' => [
            'default_user_model',
            null,
            DefaultUserModelNotFound::class,
            SafeDeploy::defaultUserModel(...),
        ],
        'empty connection' => [
            'migrations_connection',
            null,
            MigrationsConnectionNotFound::class,
            SafeDeploy::migrationsConnection(...),
        ],
        'non string user model' => [
            'default_user_model',
            fake()->numberBetween(5, 10),
            SafeDeployConfigMisconfiguration::class,
            SafeDeploy::defaultUserModel(...),
        ],
        'non string connection' => [
            'migrations_connection',
            fake()->numberBetween(5, 10),
            SafeDeployConfigMisconfiguration::class,
            SafeDeploy::migrationsConnection(...),
        ],
    ]);

it('checks SafeDeploy config should return string', function (callable $method): void {
    expect($method())->toBeString();
})->with([
    SafeDeploy::defaultUserModel(...),
    SafeDeploy::migrationsConnection(...),
]);
