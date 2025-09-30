<?php

declare(strict_types=1);

namespace SafeDeploy\Laravel\Helpers;

use Closure;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Schema\ColumnDefinition;
use SafeDeploy\Laravel\Exceptions\DefaultUserModelNotFound;
use SafeDeploy\SafeDeploy;

class DBHelper
{
    /**
     * Add common columns to the table.
     */
    public static function addCommonColumns(
        Blueprint $table,
        bool $withSoftDeletes = false,
        ?string $createdByColumn = null,
        ?string $updatedByColumn = null,
        ?string $deletedByColumn = null,
    ): void {
        /** @var ColumnDefinition[] $currentColumns */
        $currentColumns = Closure::bind(function () {
            $columns = $this->getColumns();
            foreach ($columns as $column) {
                /** @var string $name */
                $name = $column['name'];
                $this->removeColumn($name);
            }

            return $columns;
        }, $table, Blueprint::class)();

        $table->id();

        Closure::bind(function () use ($currentColumns): void {
            foreach ($currentColumns as $currentColumn) {
                $this->addColumnDefinition($currentColumn);
            }
        }, $table, Blueprint::class)();

        $table->timestamps();
        self::addUserStampsTo(
            $table,
            $withSoftDeletes,
            createdByColumn: $createdByColumn,
            updatedByColumn: $updatedByColumn,
            deletedByColumn: $deletedByColumn
        );

        if ($withSoftDeletes) {
            $table->softDeletes();
        }
    }

    /**
     * Add user IDs and foreign keys to the table.
     *
     * @throws DefaultUserModelNotFound
     */
    public static function addUserStampsTo(
        Blueprint $table,
        bool $softDeletes = true,
        bool $createsForeignKeys = true,
        ?string $usersTable = null,
        ?string $createdByColumn = null,
        ?string $updatedByColumn = null,
        ?string $deletedByColumn = null,
    ): void {
        [$createdBy, $updatedBy, $deletedBy] = self::getUserStampsColumns($createdByColumn, $updatedByColumn, $deletedByColumn);

        $table->foreignId($createdBy)->nullable();
        $table->foreignId($updatedBy)->nullable();
        if ($softDeletes) {
            $table->foreignId($deletedBy)->nullable();
        }

        if ($createsForeignKeys) {
            $usersTable ??= SafeDeploy::defaultUserTable();
            $table->foreign($createdBy)->references('id')->on($usersTable);
            $table->foreign($updatedBy)->references('id')->on($usersTable);
            if ($softDeletes) {
                $table->foreign($deletedBy)->references('id')->on($usersTable);
            }
        }
    }

    /**
     * Drop user IDs and foreign keys from the table.
     */
    public static function dropUserStampsFrom(
        Blueprint $table,
        bool $softDeletes = true,
        bool $dropsForeignKeys = true,
        ?string $createdByColumn = null,
        ?string $updatedByColumn = null,
        ?string $deletedByColumn = null,
    ): void {
        [$createdBy, $updatedBy, $deletedBy] = self::getUserStampsColumns($createdByColumn, $updatedByColumn, $deletedByColumn);

        if ($dropsForeignKeys) {
            $table->dropForeign([$createdBy]);
            $table->dropForeign([$updatedBy]);
            if ($softDeletes) {
                $table->dropForeign([$deletedBy]);
            }
        }

        $table->dropColumn($createdBy);
        $table->dropColumn($updatedBy);
        if ($softDeletes) {
            $table->dropColumn($deletedBy);
        }
    }

    /**
     * Get the name for the user stamp columns
     *
     * @return array{string, string, string}
     */
    private static function getUserStampsColumns(
        ?string $createdByColumn = null,
        ?string $updatedByColumn = null,
        ?string $deletedByColumn = null,
    ): array {
        /** @var string $createdByColumnName */
        $createdByColumnName = (blank($createdByColumn) ? config('safe-deploy.user_stamp_columns.created_by') : $createdByColumn) ?: 'created_by';
        /** @var string $updatedByColumnName */
        $updatedByColumnName = (blank($updatedByColumn) ? config('safe-deploy.user_stamp_columns.updated_by') : $updatedByColumn) ?: 'updated_by';
        /** @var string $deletedByColumnName */
        $deletedByColumnName = (blank($deletedByColumn) ? config('safe-deploy.user_stamp_columns.deleted_by') : $deletedByColumn) ?: 'deleted_by';

        return [$createdByColumnName, $updatedByColumnName, $deletedByColumnName];
    }
}
