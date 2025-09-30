<?php

declare(strict_types=1);

namespace SafeDeploy\Filament\Pages;

use Filament\Resources\Pages\ViewRecord;
use SafeDeploy\Filament\Concerns\HasDefaultHeaderActions;
use SafeDeploy\Filament\Concerns\HasDetailSubheading;
use SafeDeploy\Filament\Concerns\HasParentBreadcrumbs;

class BaseViewPage extends ViewRecord
{
    use HasDefaultHeaderActions;
    use HasDetailSubheading;
    use HasParentBreadcrumbs;
}
