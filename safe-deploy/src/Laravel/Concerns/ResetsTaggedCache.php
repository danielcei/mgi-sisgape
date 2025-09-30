<?php

declare(strict_types=1);

namespace SafeDeploy\Laravel\Concerns;

use Illuminate\Database\Eloquent\Model;
use SafeDeploy\Laravel\Observers\TaggedCacheObserver;

/**
 * @mixin Model
 */
trait ResetsTaggedCache
{
    public static function bootResetsTaggedCache(): void
    {
        self::observe(TaggedCacheObserver::class);
    }
}
