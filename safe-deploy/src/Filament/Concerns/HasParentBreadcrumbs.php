<?php

declare(strict_types=1);

namespace SafeDeploy\Filament\Concerns;

use Filament\Resources\Pages\EditRecord;
use Filament\Resources\Pages\ViewRecord;
use Filament\Resources\Resource;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Model;
use SafeDeploy\Filament\Contracts\ParentBreadcrumbLinkable;
use SafeDeploy\Filament\Data\ParentBreadcrumbConfig;

/**
 * @mixin EditRecord|ViewRecord
 */
trait HasParentBreadcrumbs
{
    /**
     * @return array<string, Htmlable|string|null>
     */
    public function getBreadcrumbs(): array
    {
        return $this->buildBreadcrumbs(static::getResource(), $this->getRecord());
    }

    /**
     * @param  class-string|null  $resource
     * @return array<string, Htmlable|string|null>
     */
    protected function buildBreadcrumbs(?string $resource, ?Model $record, bool $withLeaf = false): array
    {
        if ($resource === null || ! $this->areValidBreadcrumbParameters($resource, $record)) {
            return [];
        }

        $breadcrumbs = $this->buildResourceBreadcrumbs($resource, $record);

        if ($withLeaf) {
            return $this->appendLeafBreadcrumb($breadcrumbs, $resource, $record);
        }

        return $breadcrumbs;
    }

    /**
     * @param  array<string, Htmlable|string|null>  $breadcrumbs
     * @param  class-string  $resource
     * @return array<string, Htmlable|string|null>
     */
    private function appendLeafBreadcrumb(array $breadcrumbs, string $resource, ?Model $record): array
    {
        /** @var class-string<resource> $resource */
        $breadcrumbs[$resource::getUrl('view', ['record' => $record])] = $resource::getRecordTitle($record);

        return $breadcrumbs;
    }

    /**
     * Verifica se os parâmetros são válidos para gerar breadcrumbs
     */
    private function areValidBreadcrumbParameters(?string $resource, ?Model $record): bool
    {
        return $resource !== null && $record instanceof Model;
    }

    /**
     * @param  class-string  $resource
     * @return array<string, Htmlable|string|null>
     */
    private function buildLinkableResourceBreadcrumbs(string $resource, ?Model $record): array
    {
        /** @var class-string<ParentBreadcrumbLinkable> $resource */
        $parentConfig = $resource::getParentBreadcrumbConfig($record);

        if ($parentConfig && class_exists($parentConfig->resource)) {
            return $this->buildNestedBreadcrumbs($resource, $parentConfig);
        }

        return $this->buildStandardResourceBreadcrumb($resource);
    }

    /**
     * @return array<string, Htmlable|string|null>
     */
    private function buildNestedBreadcrumbs(string $resource, ParentBreadcrumbConfig $parentConfig): array
    {
        $parentBreadcrumbs = $this->buildBreadcrumbs(
            $parentConfig->resource,
            $parentConfig->record,
            withLeaf: true
        );

        if ($parentConfig->childless) {
            return $parentBreadcrumbs;
        }

        /** @var class-string<ParentBreadcrumbLinkable&resource> $resource */
        $currentBreadcrumb = [
            $resource::getUrl(parameters: $parentConfig->parameters) => $resource::getBreadcrumb(),
        ];

        return array_merge($parentBreadcrumbs, $currentBreadcrumb);
    }

    /**
     * @return array<string, Htmlable|string|null>
     */
    private function buildResourceBreadcrumbs(string $resource, ?Model $record): array
    {
        if (! class_exists($resource)) {
            return [];
        }

        if (is_subclass_of($resource, ParentBreadcrumbLinkable::class)) {
            return $this->buildLinkableResourceBreadcrumbs($resource, $record);
        }

        return $this->buildStandardResourceBreadcrumb($resource);
    }

    /**
     * @param  class-string  $resource
     * @return array<string, Htmlable|string|null>
     */
    private function buildStandardResourceBreadcrumb(string $resource): array
    {
        /** @var class-string<resource> $resource */
        return [$resource::getUrl() => $resource::getBreadcrumb()];
    }
}
