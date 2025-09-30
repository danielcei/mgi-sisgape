<?php

declare(strict_types=1);

namespace App\Providers\Mps;

use Filament\AvatarProviders\Contracts\AvatarProvider;
use Filament\Facades\Filament;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class MPSAvatarProvider implements AvatarProvider
{
    protected const int INITIALS_SIZE = 2;

    public function get(Authenticatable|Model $record): string
    {
        $name = str(Filament::getNameForDefaultAvatar($record))
            ->trim()
            ->explode(' ')
            ->map(fn (string $segment): string => filled($segment) ? $this->initials($segment) : '')
            ->join(' ');

        return Str::of(urlencode($name))
            ->prepend('https://ui-avatars.com/api/?name=')
            ->append('&color=FFFFFF&background=09090b')
            ->toString();
    }

    private function initials(string $name): string
    {
        $parts = preg_split("/\s+/", trim(strtoupper($name))) ?: [];

        if (count($parts) === 1) {
            return in_array(substr($parts[0], 0, self::INITIALS_SIZE), ['', '0'], true) ? '' : substr($parts[0], 0, self::INITIALS_SIZE);
        }

        return substr($parts[0], 0, 1).substr(end($parts) ?: '', 0, 1);
    }
}
