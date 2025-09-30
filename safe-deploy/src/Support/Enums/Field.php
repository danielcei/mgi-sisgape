<?php

declare(strict_types=1);

namespace SafeDeploy\Support\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Facades\FilamentIcon;

enum Field: string implements HasColor, HasIcon, HasLabel
{
    case ID = 'id';

    public function getColor(): string
    {
        return match ($this) {
            self::ID => 'id',
        };
    }

    public function getIcon(): string
    {
        return (string) match ($this) {
            self::ID => FilamentIcon::resolve('safe-deploy::field.id'),
        };
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::ID => __('ID'),
        };
    }
}
