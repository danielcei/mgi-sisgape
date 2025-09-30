<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum Status: string implements HasLabel, HasIcon, HasColor
{
    case ACTIVE = 'active';
    case SUSPENDED = 'suspended';
    case IN_TYPING = 'in_typing';
    case UNDER_REVIEW = 'under_review';

    public function getLabel(): ?string
    {
        return match ($this) {
            self::ACTIVE => __('Actived'),
            self::SUSPENDED => __('Suspended'),
            self::IN_TYPING => __('In Typing'),
            self::UNDER_REVIEW => __('Under Review'),
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::ACTIVE => 'success',
            self::SUSPENDED => 'danger',
            self::IN_TYPING => 'warning',
            self::UNDER_REVIEW => 'info',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::ACTIVE => 'heroicon-o-check-circle',
            self::SUSPENDED => 'heroicon-o-exclamation-circle',
            self::IN_TYPING => 'heroicon-o-pencil',
            self::UNDER_REVIEW => 'heroicon-o-eye',
        };
    }

    public static function inTypingOptions(): array
    {
        return [
            self::IN_TYPING->value => self::IN_TYPING->getLabel(),
        ];
    }
}
