<?php

declare(strict_types=1);

namespace SafeDeploy\Filament\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;
use Filament\Support\Enums\ActionSize;
use SafeDeploy\Support\Enums\Action;

class ManageSimpleResource extends ManageRecords
{
    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon(Action::CREATE->getIcon())
                ->size(ActionSize::Small),
        ];
    }
}
