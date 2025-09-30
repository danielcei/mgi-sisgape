<?php

declare(strict_types=1);

namespace SafeDeploy\Filament\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\ActionSize;
use Override;
use SafeDeploy\Support\Enums\Action;

class BaseListPage extends ListRecords
{
    protected ?string $maxContentWidth = 'full';

    #[Override]
    public function getBreadcrumbs(): array
    {
        $breadcrumbs = parent::getBreadcrumbs();

        // Remove the "list" step.
        array_splice($breadcrumbs, -1);

        return $breadcrumbs;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon(Action::CREATE->getIcon())
                ->size(ActionSize::Small),
        ];
    }
}
