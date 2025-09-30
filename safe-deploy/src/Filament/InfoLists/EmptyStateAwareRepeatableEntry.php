<?php

declare(strict_types=1);

namespace SafeDeploy\Filament\InfoLists;

use Filament\Infolists\Components\RepeatableEntry;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Override;
use SafeDeploy\Filament\Concerns\HasEmptyState;

class EmptyStateAwareRepeatableEntry extends RepeatableEntry
{
    use HasEmptyState;

    #[Override]
    public function render(): View
    {
        $state = $this->getState();

        if ($state instanceof Collection) {
            $state = $state->all();
        }

        $state = Arr::wrap($state);

        if (empty($state)) {
            $this->view = 'safe-deploy::filament.resources.components.infoLists.empty-state-aware-repeatable-entry';
        }

        return parent::render();
    }
}
