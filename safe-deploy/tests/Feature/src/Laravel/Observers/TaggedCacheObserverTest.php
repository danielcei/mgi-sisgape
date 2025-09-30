<?php

declare(strict_types=1);

use Carbon\Carbon;
use Illuminate\Support\Str;
use Workbench\App\Models\User;

uses()->group('Observers', 'TaggedCacheObserver');

it('clears tagged cache after model crud action', function (callable $action, ?callable $before = null): void {
    $cacheValue = Str::random();
    $cacheKey = Str::random();

    User::factory()->create();

    if ($before !== null) {
        $before();
    }

    cache()->tags([User::class])->rememberForever($cacheKey, static fn () => $cacheValue);

    $cacheBeforeAction = cache()->tags([User::class])->get($cacheKey);

    $action();

    $this->assertEquals($cacheValue, $cacheBeforeAction);
    $this->assertNull(cache()->tags([User::class])->get($cacheKey));
})
    ->with([
        'Creating' => fn () => User::factory()->create(),
        'Updating' => fn () => User::first()->update(['created_at' => Carbon::now()]),
        'Deleting' => fn () => User::first()->delete(),
        'Restoring' => [
            fn () => User::onlyTrashed()->first()->restore(),
            fn () => User::first()->delete(),
        ],
    ]);
