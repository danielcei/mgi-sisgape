<?php

declare(strict_types=1);

namespace SafeDeploy\Filament\Concerns;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ViewRecord;
use Filament\Support\Enums\ActionSize;
use Filament\Support\Facades\FilamentIcon;
use InvalidArgumentException;
use SafeDeploy\Filament\Resources\BaseResource;
use SafeDeploy\Support\Enums\Action;

/**
 * @mixin EditRecord|ViewRecord
 */
trait HasDefaultHeaderActions
{
    /**
     * @return array<int, Actions\Action|Actions\ActionGroup>
     */
    protected function getHeaderActions(): array
    {
        // @Todo Add - restore, force delete, etc. - authorizations

        /** @var class-string<BaseResource> $resource */
        $resource = static::getResource();

        return match (true) {
            // @phpstan-ignore-next-line
            $this instanceof ViewRecord => [
                ...$resource::getViewHeaderActions(),

                Actions\EditAction::make()
                    ->icon(Action::UPDATE->getIcon())
                    ->size(ActionSize::Small),

                Actions\ActionGroup::make([
                    ...$resource::getViewHeaderGroupedActions(),

                    Actions\ActionGroup::make([
                        Actions\ReplicateAction::make()
                            ->icon(Action::REPLICATE->getIcon()),
                    ])->dropdown(false),

                    Actions\DeleteAction::make()
                        ->icon(Action::DELETE->getIcon()),

                    //                    Actions\ActionGroup::make([
                    //                        Actions\Action::make('view-audit-logs')
                    //                            ->icon(FilamentIcon::resolve('safe-deploy::logs'))
                    //                            ->label(__('View audit logs'))
                    //                            ->url(fn (Model $record): string => AuditResource::getUrl(
                    //                                parameters: [
                    //                                    'tableFilters' => [
                    //                                        'auditable_type' => [
                    //                                            'value' => $record::class,
                    //                                        ],
                    //                                        'auditable_id' => [
                    //                                            'value' => $record->getKey(),
                    //                                        ],
                    //                                    ],
                    //                                ]
                    //                            ))
                    //                            ->hidden(fn (Model $record): bool => ! $record instanceof Auditable),
                    //                    ])
                    //                        ->dropdown(false),
                ]),
            ],

            // @phpstan-ignore-next-line
            $this instanceof EditRecord => [
                ...$resource::getEditHeaderActions(),

                Actions\ViewAction::make()
                    ->icon(Action::VIEW->getIcon())
                    ->size(ActionSize::Small),

                Actions\ActionGroup::make([
                    ...$resource::getEditHeaderGroupedActions(),

                    Actions\ActionGroup::make([
                        Actions\ReplicateAction::make()
                            ->icon(Action::REPLICATE->getIcon()),
                    ])->dropdown(false),

                    Actions\DeleteAction::make()
                        ->icon(Action::DELETE->getIcon()),
                ]),
            ],

            default => throw new InvalidArgumentException('The class using this trait is not a valid class')
        };
    }
}
