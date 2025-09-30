<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use SafeDeploy\SafeDeploy;

return new class extends Migration
{
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = $this->getTableNames();

        foreach ($tables as $tableName) {
            if (is_string($tableName)
                && Schema::connection(SafeDeploy::migrationsConnection())->hasTable($tableName)
                && Schema::connection(SafeDeploy::migrationsConnection())->hasColumn($tableName, 'deleted_at')
            ) {
                Schema::connection(SafeDeploy::migrationsConnection())->table($tableName, function (Blueprint $table): void {
                    $table->dropColumn('deleted_at');
                });
            }
        }
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tables = $this->getTableNames();

        foreach ($tables as $tableName) {
            if (is_string($tableName)
                && Schema::connection(SafeDeploy::migrationsConnection())->hasTable($tableName)
                && Schema::connection(SafeDeploy::migrationsConnection())->hasColumn($tableName, 'deleted_at')
            ) {
                continue;
            }

            if (is_string($tableName)) {
                Schema::connection(SafeDeploy::migrationsConnection())->table($tableName, function (Blueprint $table): void {
                    $table->datetime('deleted_at')->nullable()->after('updated_at');
                });
            }
        }
    }

    /**
     * @return array<string>
     */
    private function getTableNames(): array
    {
        $tableNames = (array) config('permission.table_names');

        if ($tableNames === []) {
            throw new Exception('Error: config/permission.php not loaded. Run [php artisan config:clear] and try again.');
        }

        return [
            Arr::get($tableNames, 'roles'),
            Arr::get($tableNames, 'permissions'),
        ];
    }
};
