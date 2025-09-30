<?php

declare(strict_types=1);

namespace SafeDeploy\Support\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Filament\Support\Facades\FilamentIcon;

enum Action: string implements HasColor, HasIcon, HasLabel
{
    case CREATE = 'create';
    case DELETE = 'delete';
    case FORCE_DELETE = 'force-delete';
    case REPLICATE = 'replicate';
    case RESTORE = 'restore';
    case UPDATE = 'update';
    case VIEW = 'view';
    case VIEW_ANY = 'view-any';

    public function getColor(): string
    {
        return match ($this) {
            self::CREATE, self::REPLICATE => 'success',
            self::DELETE, self::FORCE_DELETE => 'danger',
            self::RESTORE => 'warning',
            self::UPDATE => 'info',
            self::VIEW, self::VIEW_ANY => 'gray',
        };
    }

    public function getIcon(): string
    {
        return (string) match ($this) {
            self::CREATE => FilamentIcon::resolve('safe-deploy::action.create'),
            self::DELETE => FilamentIcon::resolve('safe-deploy::action.delete'),
            self::FORCE_DELETE => FilamentIcon::resolve('safe-deploy::action.force-delete'),
            self::REPLICATE => FilamentIcon::resolve('safe-deploy::action.replicate'),
            self::RESTORE => FilamentIcon::resolve('safe-deploy::action.restore'),
            self::UPDATE => FilamentIcon::resolve('safe-deploy::action.update'),
            self::VIEW => FilamentIcon::resolve('safe-deploy::action.view'),
            self::VIEW_ANY => FilamentIcon::resolve('safe-deploy::action.view-any'),
        };
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::CREATE => __('Create'),
            self::DELETE => __('Delete'),
            self::FORCE_DELETE => __('Force delete'),
            self::REPLICATE => __('Replicate'),
            self::RESTORE => __('Restore'),
            self::UPDATE => __('Update'),
            self::VIEW => __('View'),
            self::VIEW_ANY => __('View any'),
        };
    }
}
