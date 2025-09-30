<?php

declare(strict_types=1);

namespace App;

final class MPS
{
    public const int GRID_SIZE_ONE = 1;

    public const int GRID_SIZE_THREE = 3;

    public const int GRID_SIZE_TWO = 2;

    public static function version(): string
    {
        return '2.0.1';
    }
}
