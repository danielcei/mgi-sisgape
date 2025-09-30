<?php

declare(strict_types=1);

namespace SafeDeploy\Laravel\Observers;

use Illuminate\Database\Eloquent\Model;

class TaggedCacheObserver
{
    public function saving(Model $model): void
    {
        cache()->tags([$model::class])->flush();
    }
}
