<?php

declare(strict_types=1);

namespace SafeDeploy\Laravel\Observers;

use Illuminate\Support\Facades\Auth;
use SafeDeploy\Laravel\Contracts\Userstampable;

class UserstampsObserver
{
    public function creating(Userstampable $model): void
    {
        $model->setCreatedBy(Auth::id());
    }

    public function deleting(Userstampable $model): void
    {
        $model->setDeletedBy(Auth::id());
    }

    public function restoring(Userstampable $model): void
    {
        $model->setDeletedBy(null);
    }

    public function saving(Userstampable $model): void
    {
        $model->setUpdatedBy(Auth::id());
    }
}
