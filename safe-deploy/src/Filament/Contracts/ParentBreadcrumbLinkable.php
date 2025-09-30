<?php

declare(strict_types=1);

namespace SafeDeploy\Filament\Contracts;

use Illuminate\Database\Eloquent\Model;
use SafeDeploy\Filament\Data\ParentBreadcrumbConfig;

interface ParentBreadcrumbLinkable
{
    public static function getParentBreadcrumbConfig(?Model $record): ?ParentBreadcrumbConfig;
}
