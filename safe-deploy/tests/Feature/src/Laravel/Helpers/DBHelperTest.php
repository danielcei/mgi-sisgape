<?php

declare(strict_types=1);

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

uses()->group('Helpers', 'DBHelper');

it('it should create all common columns respecting softDelete parameter', function (bool $softDeletes): void {
    $tableName = fake()->word();

    Schema::create($tableName, function (Blueprint $table) use ($softDeletes): void {
        $table->common($softDeletes);
    });

    $columns = Schema::getColumnListing($tableName);

    expect($columns)->toContain('id')
        ->and($columns)->toContain('created_at')
        ->and($columns)->toContain('updated_at')
        ->and($columns)->toContain('created_by')
        ->and($columns)->toContain('updated_by');

    if ($softDeletes) {
        expect($columns)->toContain('deleted_at')
            ->and($columns)->toContain('deleted_by');
    }

    Schema::dropIfExists($tableName);
})
    ->with([
        'with soft deletes' => true,
        'without soft deletes' => false,
    ]);

it('it should be able to delete user stamps columns', function (): void {
    $tableName = fake()->word();

    Schema::create($tableName, function (Blueprint $table): void {
        $table->common(withSoftDeletes: true);
    });

    $columnsAfterCreate = Schema::getColumnListing($tableName);

    expect($columnsAfterCreate)->toContain('created_by')
        ->and($columnsAfterCreate)->toContain('updated_by')
        ->and($columnsAfterCreate)->toContain('deleted_by');

    Schema::table($tableName, function (Blueprint $table): void {
        $table->dropUserStamps();
    });

    $columnsAfterDelete = Schema::getColumnListing($tableName);

    expect($columnsAfterDelete)->not->toContain('created_by')
        ->and($columnsAfterDelete)->not->toContain('updated_by')
        ->and($columnsAfterDelete)->not->toContain('deleted_by');

    Schema::dropIfExists($tableName);
});

it('it should be able to add common columns in alter table', function (): void {
    $tableName = fake()->word();

    Schema::create($tableName, function (Blueprint $table): void {
        $table->id();
        $table->string('name');
        $table->string('description');
        $table->timestamps();
    });

    Schema::table($tableName, function (Blueprint $table): void {
        $table->softDeletes();
        $table->addUserStamps();
    });

    $columns = Schema::getColumnListing($tableName);

    expect($columns)->toContain('id')
        ->and($columns)->toContain('name')
        ->and($columns)->toContain('description')
        ->and($columns)->toContain('created_at')
        ->and($columns)->toContain('updated_at')
        ->and($columns)->toContain('deleted_at')
        ->and($columns)->toContain('created_by')
        ->and($columns)->toContain('updated_by')
        ->and($columns)->toContain('deleted_by');

    Schema::dropIfExists($tableName);
});

it('it should not create relations if createForeignKeys is false', function (): void {
    $tableName = fake()->word();

    Schema::create($tableName, function (Blueprint $table): void {
        $table->id();
    });

    Schema::table($tableName, function (Blueprint $table): void {
        $table->addUserStamps(createsForeignKeys: false);
    });

    $relations = Schema::getForeignKeys($tableName);

    expect($relations)->toBeEmpty();

    Schema::dropIfExists($tableName);
});

it('it should be able to use another table name for user stamps', function (): void {
    $tableName = fake()->word();

    Schema::create($tableName, function (Blueprint $table) use ($tableName): void {
        $table->id();
        $table->addUserStamps(usersTable: $tableName);
    });

    $relations = Schema::getForeignKeys($tableName);

    collect($relations)->each(
        fn ($relation) => $this->assertEquals(Arr::get($relation, 'foreign_table'), $tableName)
    );

    Schema::dropIfExists($tableName);
});

it('it should be able to use custom user stamp columns in common macro', function (): void {
    [$tableName, $createdBy, $updatedBy, $deleteBy] = fake()->words(4);

    Schema::create($tableName, function (Blueprint $table) use ($createdBy, $updatedBy, $deleteBy): void {
        $table->common(
            withSoftDeletes: true,
            createdByColumn: $createdBy,
            updatedByColumn: $updatedBy,
            deletedByColumn: $deleteBy
        );
    });

    $columns = Schema::getColumnListing($tableName);

    expect($columns)->toContain($createdBy)
        ->and($columns)->toContain($updatedBy)
        ->and($columns)->toContain($deleteBy)
        ->and($columns)->not->toContain('created_by')
        ->and($columns)->not->toContain('updated_by')
        ->and($columns)->not->toContain('deleted_by');

    Schema::dropIfExists($tableName);
});

it('it should be able to use config values for user stamp columns in common macro', function (): void {
    [$tableName, $createdBy, $updatedBy, $deleteBy] = fake()->words(4);

    $defaultCreatedBy = Config::get('safe-deploy.user_stamp_columns.created_by');
    $defaultUpdatedBy = Config::get('safe-deploy.user_stamp_columns.updated_by');
    $defaultDeletedBy = Config::get('safe-deploy.user_stamp_columns.deleted_by');

    Config::set('safe-deploy.user_stamp_columns.created_by', $createdBy);
    Config::set('safe-deploy.user_stamp_columns.updated_by', $updatedBy);
    Config::set('safe-deploy.user_stamp_columns.deleted_by', $deleteBy);

    Schema::create($tableName, function (Blueprint $table): void {
        $table->common(withSoftDeletes: true);
    });

    $columns = Schema::getColumnListing($tableName);

    expect($columns)->toContain($createdBy)
        ->and($columns)->toContain($updatedBy)
        ->and($columns)->toContain($deleteBy)
        ->and($columns)->not->toContain('created_by')
        ->and($columns)->not->toContain('updated_by')
        ->and($columns)->not->toContain('deleted_by');

    Config::set('safe-deploy.user_stamp_columns.created_by', $defaultCreatedBy);
    Config::set('safe-deploy.user_stamp_columns.updated_by', $defaultUpdatedBy);
    Config::set('safe-deploy.user_stamp_columns.deleted_by', $defaultDeletedBy);

    Schema::dropIfExists($tableName);
});

it('it should be able to use default values for user stamp columns in common macro when config is missing', function (?string $configValue): void {
    $tableName = fake()->word();

    $defaultCreatedBy = Config::get('safe-deploy.user_stamp_columns.created_by');
    $defaultUpdatedBy = Config::get('safe-deploy.user_stamp_columns.updated_by');
    $defaultDeletedBy = Config::get('safe-deploy.user_stamp_columns.deleted_by');

    Config::set('safe-deploy.user_stamp_columns.created_by', $configValue);
    Config::set('safe-deploy.user_stamp_columns.updated_by', $configValue);
    Config::set('safe-deploy.user_stamp_columns.deleted_by', $configValue);

    Schema::create($tableName, function (Blueprint $table): void {
        $table->common(withSoftDeletes: true);
    });

    $columns = Schema::getColumnListing($tableName);

    expect($columns)->toContain('created_by')
        ->and($columns)->toContain('updated_by')
        ->and($columns)->toContain('deleted_by');

    Config::set('safe-deploy.user_stamp_columns.created_by', $defaultCreatedBy);
    Config::set('safe-deploy.user_stamp_columns.updated_by', $defaultUpdatedBy);
    Config::set('safe-deploy.user_stamp_columns.deleted_by', $defaultDeletedBy);

    Schema::dropIfExists($tableName);
})->with([
    '',
    null,
]);

it('it should be able to delete user stamps using custom column names', function (): void {
    [$tableName, $createdBy, $updatedBy, $deleteBy] = fake()->words(4);

    Schema::create($tableName, function (Blueprint $table) use ($createdBy, $updatedBy, $deleteBy): void {
        $table->common(
            withSoftDeletes: true,
            createdByColumn: $createdBy,
            updatedByColumn: $updatedBy,
            deletedByColumn: $deleteBy
        );
    });

    $columnsAfterCreate = Schema::getColumnListing($tableName);

    expect($columnsAfterCreate)->toContain($createdBy)
        ->and($columnsAfterCreate)->toContain($updatedBy)
        ->and($columnsAfterCreate)->toContain($deleteBy);

    Schema::table($tableName, function (Blueprint $table) use ($createdBy, $updatedBy, $deleteBy): void {
        $table->dropUserStamps(
            createdByColumn: $createdBy,
            updatedByColumn: $updatedBy,
            deletedByColumn: $deleteBy
        );
    });

    $columnsAfterDelete = Schema::getColumnListing($tableName);

    expect($columnsAfterDelete)->not->toContain($createdBy)
        ->and($columnsAfterDelete)->not->toContain($updatedBy)
        ->and($columnsAfterDelete)->not->toContain($deleteBy);

    Schema::dropIfExists($tableName);
});

it('it should be able to use add user stamps with custom columns', function (): void {
    [$tableName, $createdBy, $updatedBy, $deleteBy] = fake()->words(4);

    Schema::create($tableName, function (Blueprint $table) use ($createdBy, $updatedBy, $deleteBy, $tableName): void {
        $table->id();
        $table->addUserStamps(usersTable: $tableName, createdByColumn: $createdBy, updatedByColumn: $updatedBy, deletedByColumn: $deleteBy);
    });

    $columnsAfterDelete = Schema::getColumnListing($tableName);

    expect($columnsAfterDelete)->toContain($createdBy)
        ->and($columnsAfterDelete)->toContain($updatedBy)
        ->and($columnsAfterDelete)->toContain($deleteBy);

    Schema::dropIfExists($tableName);
});
