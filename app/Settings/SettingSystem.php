<?php

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

class SettingSystem extends Settings
{

    public static function group(): string
    {
        return 'setting';
    }
}
