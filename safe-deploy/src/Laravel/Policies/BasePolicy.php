<?php

declare(strict_types=1);

namespace SafeDeploy\Laravel\Policies;

use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Contracts\Auth\Access\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use SafeDeploy\Laravel\Helpers\PermissionHelper;

abstract class BasePolicy
{
    use HandlesAuthorization;

    /**
     * @param  array{0: Authorizable, 1?: mixed}  $args
     */
    public function __call(string $method, array $args): bool
    {
        [$authorizable, $model] = [...$args, null];

        if ($model === null) {
            $model = Str::replaceEnd('Policy', '', static::class);
        }

        $modelName = is_object($model) || is_string($model) ? class_basename($model) : null;
        if (! $modelName) {
            return false;
        }

        return $authorizable->can(PermissionHelper::qualifiedName($method, $modelName));
    }

    public function create(Authorizable $authorizable, ?Model $model = null): bool
    {
        return $this->__call('create', [$authorizable, $model]);
    }

    public function delete(Authorizable $authorizable, ?Model $model = null): bool
    {
        return $this->__call('delete', [$authorizable, $model]);
    }

    public function forceDelete(Authorizable $authorizable, ?Model $model = null): bool
    {
        return $this->__call('forceDelete', [$authorizable, $model]);
    }

    public function replicate(Authorizable $authorizable, ?Model $model = null): bool
    {
        return $this->__call('replicate', [$authorizable, $model]);
    }

    public function restore(Authorizable $authorizable, ?Model $model = null): bool
    {
        return $this->__call('restore', [$authorizable, $model]);
    }

    public function runAction(): bool
    {
        return true;
    }

    public function runDestructiveAction(): bool
    {
        return true;
    }

    public function update(Authorizable $authorizable, ?Model $model = null): bool
    {
        return $this->__call('update', [$authorizable, $model]);
    }

    public function view(Authorizable $authorizable, ?Model $model = null): bool
    {
        return $this->__call('view', [$authorizable, $model]);
    }

    public function viewAny(Authorizable $authorizable, mixed $model = null): bool
    {
        return $this->__call('viewAny', [$authorizable, $model]);
    }

    public function viewMenu(Authorizable $authorizable, mixed $model = null): bool
    {
        return $this->__call('viewMenu', [$authorizable, $model]);
    }
}
