<?php

declare(strict_types=1);

namespace SafeDeploy\Laravel\Models;

use OwenIt\Auditing\Models\Audit as BaseAudit;
use SafeDeploy\Support\Enums\Event;

class Audit extends BaseAudit
{
    /**
     * The attributes that should be cast.
     *
     * @var array<string , string>
     */
    protected $casts = [
        'event' => Event::class,
        'old_values' => 'json',
        'new_values' => 'json',
    ];
}
