<?php

declare(strict_types=1);

use SafeDeploy\Laravel\Helpers\PermissionHelper;
use SafeDeploy\Laravel\Models\Permission;
use SafeDeploy\Laravel\Models\Role;
use SafeDeploy\Laravel\Models\User;

uses()->group('Helpers', 'PermissionHelper');

it('generates the correct qualified name', function (string $modelClass): void {
    $ability = (string) Arr::random([
        'create',
        'delete',
        'force-delete',
        'restore',
        'update',
        'view-any',
        'replicate',
        'view',
    ]);

    $qualifiedName = PermissionHelper::qualifiedName($ability, $modelClass);
    $modelName = Str::lower(class_basename($modelClass));

    expect($qualifiedName)->toBe("{$ability}-{$modelName}");
    expect($qualifiedName)->toBeKebabCase();
})
    ->with([
        Permission::class,
        Role::class,
        User::class,
    ]);
