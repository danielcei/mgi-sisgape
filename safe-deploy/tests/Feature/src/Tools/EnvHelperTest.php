<?php

declare(strict_types=1);
use SafeDeploy\Tools\EnvHelper;

uses()->group('Tools', 'EnvHelper');

it('it should be able to handle the file', function (): void {
    $testDir = sys_get_temp_dir().'/safe-deploy_test_'.uniqid();
    mkdir($testDir, 0755, true);

    $tempEnv = $testDir.'/.env';

    $randomAppName = Str::random();

    $dotEnvFakeData = [
        'APP_NAME='.$randomAppName,
    ];

    $dotEnvFakeArrayData = [
        'APP_NAME' => $randomAppName,
    ];

    file_put_contents($tempEnv, implode(PHP_EOL, $dotEnvFakeData));

    $dotEnv = new EnvHelper($tempEnv);
    expect($dotEnv->getKey('APP_NAME'))
        ->toBe($randomAppName)
        ->and($dotEnv->exists('APP_NAME'))
        ->toBeTrue()
        ->and($dotEnv->getKeys())
        ->toBe($dotEnvFakeArrayData)
        ->and($dotEnv->getKey('NEW_KEY_NAME'))
        ->toBeNull();

    $newRandomKeyValue = Str::random();
    $dotEnv->setKey('NEW_KEY_NAME', $newRandomKeyValue);

    expect($dotEnv->getKey('NEW_KEY_NAME'))
        ->toBe($newRandomKeyValue)
        ->and($dotEnv->exists('NEW_KEY_NAME'))
        ->toBeTrue();

    $randomNameForUpdateKey = Str::random();
    $dotEnv->setKey('NEW_KEY_NAME', $randomNameForUpdateKey);

    expect($dotEnv->getKey('NEW_KEY_NAME'))
        ->toBe($randomNameForUpdateKey)
        ->and($dotEnv->exists('NEW_KEY_NAME'))
        ->toBeTrue();

    unlink($tempEnv);
    rmdir($testDir);
});

it('it should be return a empty array when the file is unreachable', function (): void {
    $dotEnv = new EnvHelper('undefined_file.env');

    expect($dotEnv->getKeys())->toBe([]);
});
