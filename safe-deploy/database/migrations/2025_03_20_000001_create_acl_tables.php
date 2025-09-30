<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;
use SafeDeploy\SafeDeploy;

return new class extends Migration
{
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tableNames = (array) config('permission.table_names');

        if ($tableNames === []) {
            throw new Exception('Error: config/permission.php not found and defaults could not be merged. Please publish the package configuration before proceeding, or drop the tables manually.');
        }

        $roleHasPermissions = Arr::get($tableNames, 'role_has_permissions');
        if (is_string($roleHasPermissions) && $roleHasPermissions) {
            Schema::connection(SafeDeploy::migrationsConnection())->dropIfExists($roleHasPermissions);
        }

        $modelHasRoles = Arr::get($tableNames, 'model_has_roles');
        if (is_string($modelHasRoles) && $modelHasRoles) {
            Schema::connection(SafeDeploy::migrationsConnection())->dropIfExists($modelHasRoles);
        }

        $modelHasPermissions = Arr::get($tableNames, 'model_has_permissions');
        if (is_string($modelHasPermissions) && $modelHasPermissions) {
            Schema::connection(SafeDeploy::migrationsConnection())->dropIfExists($modelHasPermissions);
        }

        $roles = Arr::get($tableNames, 'roles');
        if (is_string($roles) && $roles) {
            Schema::connection(SafeDeploy::migrationsConnection())->dropIfExists($roles);
        }

        $permissions = Arr::get($tableNames, 'permissions');
        if (is_string($permissions) && $permissions) {
            Schema::connection(SafeDeploy::migrationsConnection())->dropIfExists($permissions);
        }
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $teams = (bool) config('permission.teams');
        $tableNames = (array) config('permission.table_names');
        $columnNames = (array) config('permission.column_names');
        $pivotRole = 'role_id';
        $pivotPermission = 'permission_id';

        if ($tableNames === []) {
            throw new Exception('Error: config/permission.php not loaded. Run [php artisan config:clear] and try again.');
        }

        if ($teams && empty(Arr::get($columnNames, 'team_foreign_key'))) {
            throw new Exception('Error: team_foreign_key on config/permission.php not loaded. Run [php artisan config:clear] and try again.');
        }

        $tablePermissionsName = Arr::get($tableNames, 'permissions');
        if (is_string($tablePermissionsName) && ! Schema::connection(SafeDeploy::migrationsConnection())->hasTable($tablePermissionsName)) {
            Schema::connection(SafeDeploy::migrationsConnection())
                ->create($tablePermissionsName, static function (Blueprint $table): void {
                    // $table->engine('InnoDB');
                    $table->common(); // See SafeDeploy DB Macros
                    $table->string('name');       // For MyISAM use string('name', 225); // (or 166 for InnoDB with Redundant/Compact row format)
                    $table->string('guard_name')->default('web')->index(); // For MyISAM use string('guard_name', 25);
                    $table->softDeletes();

                    $table->unique(['name', 'guard_name']);
                });
        }

        $rolesTableName = Arr::get($tableNames, 'roles');
        if (is_string($rolesTableName) && ! Schema::connection(SafeDeploy::migrationsConnection())->hasTable($rolesTableName)) {
            Schema::connection(SafeDeploy::migrationsConnection())
                ->create($rolesTableName, static function (Blueprint $table) use ($teams, $columnNames): void {
                    // $table->engine('InnoDB');
                    $table->common(); // See SafeDeploy DB Macros
                    $teamForeignKey = Arr::get($columnNames, 'team_foreign_key');
                    // permission.testing is a fix for sqlite testing
                    if (($teams || config('permission.testing')) && is_string($teamForeignKey)) {
                        $table->unsignedBigInteger($teamForeignKey)->nullable();
                        $table->index($teamForeignKey, 'roles_team_foreign_key_index');
                    }

                    $table->string('name');       // For MyISAM use string('name', 225); // (or 166 for InnoDB with Redundant/Compact row format)
                    $table->string('guard_name')->default('web')->index(); // For MyISAM use string('guard_name', 25);
                    $table->string('ad_group_name')->nullable()->index();
                    $table->softDeletes();

                    if ($teams || config('permission.testing')) {
                        $table->unique([$teamForeignKey, 'name', 'guard_name']);
                    }

                    if (! $teams || ! config('permission.testing')) {
                        $table->unique(['name', 'guard_name']);
                    }
                });
        }

        $modelHasPermissionsTableName = Arr::get($tableNames, 'model_has_permissions');
        if (is_string($modelHasPermissionsTableName) && ! Schema::connection(SafeDeploy::migrationsConnection())->hasTable($modelHasPermissionsTableName)) {
            Schema::connection(SafeDeploy::migrationsConnection())
                ->create($modelHasPermissionsTableName, static function (Blueprint $table) use ($tableNames, $columnNames, $pivotPermission, $teams): void {
                    $table->unsignedBigInteger($pivotPermission);

                    $table->string('model_type');

                    $modelMorphKey = Arr::get($columnNames, 'model_morph_key');
                    if (is_string($modelMorphKey)) {
                        $table->unsignedBigInteger($modelMorphKey);
                        $table->index([$modelMorphKey, 'model_type'], 'model_has_permissions_model_id_model_type_index');
                    }

                    $tablePermissions = Arr::get($tableNames, 'permissions');

                    if (is_string($tablePermissions)) {
                        $table->foreign($pivotPermission)
                            ->references('id') // permission id
                            ->on($tablePermissions)
                            ->onDelete('cascade');
                    }

                    if ($teams) {
                        $teamForeignKey = Arr::get($columnNames, 'team_foreign_key');
                        if (is_string($teamForeignKey)) {
                            $table->unsignedBigInteger($teamForeignKey);
                            $table->index($teamForeignKey, 'model_has_permissions_team_foreign_key_index');
                        }

                        $table->primary([$teamForeignKey, $pivotPermission, $modelMorphKey, 'model_type'],
                            'model_has_permissions_permission_model_type_primary');
                    }

                    if (! $teams) {
                        $table->primary([$pivotPermission, $modelMorphKey, 'model_type'],
                            'model_has_permissions_permission_model_type_primary');
                    }

                    $table->addUserStamps(softDeletes: false, createsForeignKeys: false);
                });
        }

        $modelHasRolesTableName = Arr::get($tableNames, 'model_has_roles');
        if (is_string($modelHasRolesTableName) && ! Schema::connection(SafeDeploy::migrationsConnection())->hasTable($modelHasRolesTableName)) {
            Schema::connection(SafeDeploy::migrationsConnection())
                ->create($modelHasRolesTableName, static function (Blueprint $table) use ($tableNames, $columnNames, $pivotRole, $teams): void {
                    $table->unsignedBigInteger($pivotRole);

                    $table->string('model_type');

                    $modelMorphKey = Arr::get($columnNames, 'model_morph_key');
                    if (is_string($modelMorphKey)) {
                        $table->unsignedBigInteger($modelMorphKey);
                    }

                    $table->index([Arr::get($columnNames, 'model_morph_key'), 'model_type'], 'model_has_roles_model_id_model_type_index');

                    $roles = Arr::get($tableNames, 'roles');

                    if (is_string($roles)) {
                        $table->foreign($pivotRole)
                            ->references('id') // role id
                            ->on($roles)
                            ->onDelete('cascade');
                    }

                    if ($teams) {
                        $teamForeignKey = Arr::get($columnNames, 'team_foreign_key');
                        if (is_string($teamForeignKey)) {
                            $table->unsignedBigInteger($teamForeignKey);
                        }

                        $teamForeignKey = Arr::get($columnNames, 'team_foreign_key');
                        if (is_string($teamForeignKey)) {
                            $table->index($teamForeignKey, 'model_has_roles_team_foreign_key_index');
                        }

                        $table->primary([Arr::get($columnNames, 'team_foreign_key'), $pivotRole, $pivotRole, 'model_type'],
                            'model_has_roles_role_model_type_primary');
                    }

                    if (! $teams) {
                        $table->primary([$pivotRole, 'model_id', 'model_type'],
                            'model_has_roles_role_model_type_primary');
                    }

                    $table->unsignedBigInteger('created_by')->nullable()->index();
                    $table->unsignedBigInteger('updated_by')->nullable()->index();
                    $table->unsignedBigInteger('deleted_by')->nullable()->index();
                });
        }

        $roleHasPermissionsTableName = Arr::get($tableNames, 'role_has_permissions');
        if (is_string($roleHasPermissionsTableName) && ! Schema::connection(SafeDeploy::migrationsConnection())->hasTable($roleHasPermissionsTableName)) {
            Schema::connection(SafeDeploy::migrationsConnection())
                ->create($roleHasPermissionsTableName, static function (Blueprint $table) use ($tableNames, $pivotRole, $pivotPermission): void {
                    $table->unsignedBigInteger($pivotPermission);

                    $table->unsignedBigInteger($pivotRole);

                    $permissions = Arr::get($tableNames, 'permissions');
                    if (is_string($permissions)) {
                        $table->foreign($pivotPermission)
                            ->references('id') // permission id
                            ->on($permissions)
                            ->onDelete('cascade');
                    }

                    $roles = Arr::get($tableNames, 'roles');
                    if (is_string($roles)) {
                        $table->foreign($pivotRole)
                            ->references('id') // role id
                            ->on($roles)
                            ->onDelete('cascade');
                    }

                    $table->primary([$pivotPermission, $pivotRole], 'role_has_permissions_permission_id_role_id_primary');

                    $table->unsignedBigInteger('created_by')->nullable()->index();
                    $table->unsignedBigInteger('updated_by')->nullable()->index();
                    $table->unsignedBigInteger('deleted_by')->nullable()->index();
                });
        }

        app('cache')
            // @phpstan-ignore-next-line
            ->store(config('permission.cache.store') !== 'default' ? config('permission.cache.store') : null)
            // @phpstan-ignore-next-line
            ->forget(config('permission.cache.key'));
    }
};
