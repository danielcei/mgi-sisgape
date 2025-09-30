<?php

declare(strict_types=1);

namespace SafeDeploy\Laravel\Listeners;

use Illuminate\Foundation\Events\PublishingStubs;

class ExtendLaravelStubs
{
    /**
     * Handle the given event.
     */
    public function handle(PublishingStubs $event): void
    {
        $path = __DIR__.'/../../../stubs/laravel';

        $stubs = glob("{$path}/*.stub");

        if ($stubs === false) {
            return;
        }

        /** @var array<string, string> $eventStubs */
        $eventStubs = $event->stubs;

        $stubsByName = array_flip($eventStubs);

        /** @var string[] $stubs */
        foreach ($stubs as $stub) {
            $stubsByName[basename($stub)] = realpath($stub);
        }

        /** @var array<string, string> $stubsByName */
        $event->stubs = array_flip($stubsByName);
    }
}
