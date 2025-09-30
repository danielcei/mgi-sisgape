<?php

declare(strict_types=1);

namespace SafeDeploy\Laravel\Helpers;

use Illuminate\Support\Str;
use SafeDeploy\SafeDeploy;
use SafeDeploy\Support\Enums\Action;

class PermissionHelper
{
    public static function getActionFrom(string $ability): Action|string
    {
        $class = self::getClassFromAbility($ability);

        if ($class === null) {
            return Str::headline($ability);
        }

        $action = Str::of($ability)->before($class)->beforeLast('-');

        return Action::tryFrom($action->toString()) ?: $action->headline()->toString();
    }

    public static function qualifiedName(string $ability, string $modelClass): string
    {
        return Str::of($modelClass)
            ->classBasename()
            ->prepend($ability)
            ->kebab()
            ->toString();
    }

    private static function getClassFromAbility(string $ability): ?string
    {
        $base = Str::of($ability);
        $actions = array_map(fn (Action $action): string => $action->value, Action::cases());

        // We sort the action prioritizing the ones with more dashes in the name,
        // so the "chopStart" work properly for cases like 'view-any' and 'view'.
        usort(
            $actions,
            fn (string $a, string $b): int => substr_count($b, '-') <=> substr_count($a, '-')
        );

        $classStr = $base->chopStart($actions)->after('-');

        if (! class_exists(SafeDeploy::modelNamespace($classStr->pascal()->toString()))) {
            return null;
        }

        return $classStr->toString();
    }
}
