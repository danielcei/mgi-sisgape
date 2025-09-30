<?php

declare(strict_types=1);

namespace SafeDeploy\Filament\Data;

use Illuminate\Database\Eloquent\Model;
use Spatie\LaravelData\Data;

/**
 * @property class-string $resource
 */
class ParentBreadcrumbConfig extends Data
{
    /**
     * @param  array<string, mixed>  $parameters
     */
    public function __construct(
        public string $resource,
        public Model $record,
        public array $parameters = [],
        public bool $childless = false
    ) {}
}
