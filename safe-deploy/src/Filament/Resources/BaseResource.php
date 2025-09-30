<?php

declare(strict_types=1);

namespace SafeDeploy\Filament\Resources;

use Exception;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms;
use Filament\Forms\Components\Component as FormComponent;
use Filament\Forms\Form;
use Filament\Infolists\Components;
use Filament\Infolists\Infolist;
use Filament\Pages;
use Filament\Resources\Pages\PageRegistration;
use Filament\Resources\Resource;
use Filament\Support\Enums\MaxWidth;
use Filament\Support\Facades\FilamentIcon;
use Filament\Tables;
use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Columns\Layout\Component as LayoutComponent;
use Filament\Tables\Filters\BaseFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Override;
use SafeDeploy\Filament\Contracts\AppNavigationGroup;
use SafeDeploy\Filament\Pages\BaseCreatePage;
use SafeDeploy\Filament\Pages\BaseEditPage;
use PodSafeDeployium\Filament\Pages\BaseListPage;
use SafeDeploy\Filament\Pages\BaseViewPage;
use SafeDeploy\Filament\Pages\ManageSimpleResource;
use SafeDeploy\SafeDeploy;
use SafeDeploy\Support\Enums\Action as SafeDeployAction;

abstract class BaseResource extends Resource
{
    public const int GLOBAL_SEARCH_RESULTS_LIMIT = 5;

    public static bool $simpleResource = false;

    protected static AppNavigationGroup $appNavigationGroup;

    protected static int $globalSearchResultsLimit = self::GLOBAL_SEARCH_RESULTS_LIMIT;

    /**
     * @return array<int, FormComponent>
     */
    abstract public static function formFields(): array;

    /**
     * @return array<Column|ColumnGroup|LayoutComponent>
     */
    abstract public static function tableColumns(): array;

    #[Override]
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->columns()
                    ->schema(static::formFields()),
            ]);
    }

    #[Override]
    public static function getActiveNavigationIcon(): null|Htmlable|string
    {
        if (static::$activeNavigationIcon === null) {
            return static::getNavigationIcon();
        }

        return FilamentIcon::resolve(static::$activeNavigationIcon) ?? static::$activeNavigationIcon;
    }

    /**
     * @return array<int, Action|Pages\Actions\Action>
     */
    public static function getEditHeaderActions(): array
    {
        return [];
    }

    /**
     * @return array<int, Action|Pages\Actions\Action>
     */
    public static function getEditHeaderGroupedActions(): array
    {
        return [];
    }

    #[Override]
    public static function getGlobalSearchResultUrl(Model $record): ?string
    {
        $canView = static::canView($record);

        if (static::hasPage('view') && $canView) {
            return static::getUrl('view', ['record' => $record]);
        }

        if ($canView) {
            return static::getUrl(parameters: [
                'tableAction' => 'view',
                'tableActionRecord' => $record,
            ]);
        }

        return null;
    }

    #[Override]
    public static function getNavigationGroup(): ?string
    {
        return isset(static::$appNavigationGroup) ? static::$appNavigationGroup->getLabel() : null;
    }

    #[Override]
    public static function getNavigationIcon(): null|Htmlable|string
    {
        if (static::$navigationIcon === null) {
            return null;
        }

        return FilamentIcon::resolve(static::$navigationIcon) ?? static::$navigationIcon;
    }

    #[Override]
    public static function getNavigationSort(): ?int
    {
        /** @var string[] $resources */
        $resources = config('safe-deploy.resources_ordering', []);
        $order = array_search(static::class, $resources, true);

        return is_int($order) ? $order : count($resources);
    }

    /**
     * @return array<string, PageRegistration>
     *
     * @throws Exception
     */
    #[Override]
    public static function getPages(): array
    {
        if (static::$model === null) {
            throw new Exception('Model is not set for resource '.static::class);
        }

        $modelName = Str::of(static::getModel())->classBasename();
        $modelNamePlural = $modelName->pluralStudly();
        $namespace = static::class.'\\Pages\\';

        if (static::$simpleResource) {
            /** @var class-string<ManageSimpleResource> $managePage */
            $managePage = "{$namespace}Manage{$modelNamePlural}";

            return [
                'index' => $managePage::route('/'),
            ];
        }

        /** @var class-string<BaseListPage> $listPage */
        $listPage = "{$namespace}List{$modelNamePlural}";
        /** @var class-string<BaseCreatePage> $createPage */
        $createPage = "{$namespace}Create{$modelName}";
        /** @var class-string<BaseViewPage> $viewPage */
        $viewPage = "{$namespace}View{$modelName}";
        /** @var class-string<BaseEditPage> $editPage */
        $editPage = "{$namespace}Edit{$modelName}";

        return [
            'index' => $listPage::route('/'),
            'create' => $createPage::route('/create'),
            'view' => $viewPage::route('/{record}'),
            'edit' => $editPage::route('/{record}/edit'),
        ];
    }

    /**
     * @return array<int, Action|ActionGroup|Pages\Actions\Action>
     */
    public static function getViewHeaderActions(): array
    {
        return [];
    }

    /**
     * @return array<int, Action|Pages\Actions\Action>
     */
    public static function getViewHeaderGroupedActions(): array
    {
        return [];
    }

    #[Override]
    public static function infolist(Infolist $infolist): Infolist
    {
        if (static::$simpleResource) {
            return $infolist;
        }

        return $infolist
            ->columns(1)
            ->schema([
                Components\Section::make()
                    ->schema([
                        ...static::infolistEntries(),
                    ]),
            ]);
    }

    /**
     * @return array<int, Components\Component>
     */
    public static function infolistEntries(): array
    {
        return [];
    }

    #[Override]
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label(__('ID'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                ...static::tableColumns(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Created At'))
                    ->dateTime(SafeDeploy::localizedDateTime())
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters(static::tableFilters())
            ->actions([
                ...static::tableActions(),

                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ActionGroup::make([
                        Tables\Actions\ViewAction::make(),
                        Tables\Actions\EditAction::make(),
                    ])->dropdown(false),
                    Tables\Actions\ActionGroup::make(
                        static::tableGroupActions()
                    )->dropdown(false),
                    Tables\Actions\DeleteAction::make(),
                ])
                    ->dropdownWidth(MaxWidth::Small)
                    ->tooltip(__('Actions')),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    ...static::tableBulkActions(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->persistSortInSession()
            ->persistColumnSearchesInSession()
            ->persistFiltersInSession()
            ->searchOnBlur()
            ->striped()
            ->poll()
            ->emptyStateIcon(FilamentIcon::resolve(static::$navigationIcon ?? 'tables::empty-state'))
            ->emptyStateActions([
                Tables\Actions\Action::make('create')
                    ->label(__('Create :resource', ['resource' => static::getLabel() ?? __('Resource')]))
                    ->url(static::$simpleResource ? null : static::getUrl('create'))
                    ->icon(SafeDeployAction::CREATE->getIcon())
                    ->button(),
            ]);
    }

    /**
     * @return array<int, Tables\Actions\Action>
     */
    public static function tableActions(): array
    {
        return [];
    }

    /**
     * @return array<int, Tables\Actions\Action>
     */
    public static function tableBulkActions(): array
    {
        return [];
    }

    /**
     * @return array<int, BaseFilter>
     */
    public static function tableFilters(): array
    {
        return [];
    }

    /**
     * @return array<int, Tables\Actions\Action>
     */
    public static function tableGroupActions(): array
    {
        return [];
    }
}
