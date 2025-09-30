<?php

declare(strict_types=1);

namespace SafeDeploy\Support\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Facades\FilamentIcon;

enum Event: string implements HasColor, HasIcon, HasLabel
{
    case CREATED = 'created';
    case DELETED = 'deleted';
    case RESTORED = 'restored';
    case UPDATED = 'updated';

    public function getColor(): string
    {
        return match ($this) {
            self::CREATED => 'success',
            self::DELETED => 'danger',
            self::RESTORED => 'warning',
            self::UPDATED => 'info',
        };
    }

    public function getIcon(): string
    {
        return (string) match ($this) {
            self::CREATED => FilamentIcon::resolve('safe-deploy::event.created'),
            self::DELETED => FilamentIcon::resolve('safe-deploy::event.deleted'),
            self::RESTORED => FilamentIcon::resolve('safe-deploy::event.restored'),
            self::UPDATED => FilamentIcon::resolve('safe-deploy::event.updated'),
        };
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::CREATED => __('Created'),
            self::DELETED => __('Deleted'),
            self::RESTORED => __('Restored'),
            self::UPDATED => __('Updated'),
        };
    }
}
