<?php

declare(strict_types=1);

use Workbench\App\Models\Permission;
use Workbench\App\Models\Role;
use Workbench\App\Models\User;

uses()->group('Factories');

it('checks model factory', function (string $model): void {
    $expectedRecords = fake()->numberBetween(5, 10);

    $records = (new $model)->factory()->count($expectedRecords)->create();

    $this->assertEquals($expectedRecords, $records->count());
    $this->assertContainsOnlyInstancesOf($model, $records);
    $records->each(fn ($record) => $this->assertModelExists($record));
})
    ->with([
        Permission::class,
        Role::class,
        User::class,
    ]);
