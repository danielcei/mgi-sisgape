<?php

declare(strict_types=1);

namespace SafeDeploy\Filament\Contracts;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

interface AppNavigationGroup extends HasColor, HasIcon, HasLabel
{
    public static function byLabel(string $label): ?static;
}
