<?php

declare(strict_types=1);

namespace SafeDeploy\Filament\Pages;

use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Resource;
use Override;
use SafeDeploy\Filament\Concerns\HasDefaultHeaderActions;
use SafeDeploy\Filament\Concerns\HasDetailSubheading;
use SafeDeploy\Filament\Concerns\HasParentBreadcrumbs;
use SafeDeploy\Filament\Concerns\HasRedirectUrl;

class BaseEditPage extends EditRecord
{
    use HasDefaultHeaderActions;
    use HasDetailSubheading;
    use HasParentBreadcrumbs;
    use HasRedirectUrl;

    #[Override]
    protected function getSavedNotificationTitle(): ?string
    {
        /** @var class-string<resource> $resource */
        $resource = $this->getResource();
        /** @var string $title */
        $title = $resource::getRecordTitle($this->getRecord());

        return __(':resource :title was updated!', [
            'resource' => $resource::getModelLabel(),
            'title' => $title,
        ]);
    }
}
