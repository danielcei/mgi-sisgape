<?php

declare(strict_types=1);

namespace SafeDeploy\Filament\Pages;

use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Resource;
use Override;
use SafeDeploy\Filament\Concerns\HasRedirectUrl;

class BaseCreatePage extends CreateRecord
{
    use HasRedirectUrl;

    #[Override]
    public function getBreadcrumbs(): array
    {
        $breadcrumbs = parent::getBreadcrumbs();

        // Remove the "create" step.
        array_splice($breadcrumbs, -1);

        return $breadcrumbs;
    }

    #[Override]
    protected function getCreatedNotificationTitle(): ?string
    {
        /** @var class-string<resource> $resource */
        $resource = $this->getResource();
        /** @var string $title */
        $title = $resource::getRecordTitle($this->getRecord());

        return __(':resource :title was created!', [
            'resource' => $resource::getModelLabel(),
            'title' => $title,
        ]);
    }
}
