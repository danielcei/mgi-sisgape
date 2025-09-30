<?php

declare(strict_types=1);

namespace SafeDeploy\Filament\Concerns;

use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;

/**
 * @mixin Page
 */
trait HasRedirectUrl
{
    protected function getRedirectUrl(): string
    {
        /** @var class-string<resource> $resource */
        $resource = $this->getResource();

        return $this->previousUrl ?? $resource::getUrl();
    }
}
