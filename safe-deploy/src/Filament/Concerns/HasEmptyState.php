<?php

declare(strict_types=1);

namespace SafeDeploy\Filament\Concerns;

use Closure;
use Filament\Support\Facades\FilamentIcon;

trait HasEmptyState
{
    protected null|Closure|string $emptyStateDescription = null;

    protected null|Closure|string $emptyStateHeading = null;

    protected null|Closure|string $emptyStateIcon = null;

    public function emptyStateDescription(null|Closure|string $description): static
    {
        $this->emptyStateDescription = $description;

        return $this;
    }

    public function emptyStateHeading(null|Closure|string $heading): static
    {
        $this->emptyStateHeading = $heading;

        return $this;
    }

    public function emptyStateIcon(null|Closure|string $icon): static
    {
        $this->emptyStateIcon = $icon;

        return $this;
    }

    public function getEmptyStateDescription(): ?string
    {
        $result = $this->evaluate($this->emptyStateDescription);

        if (! is_string($result)) {
            return null;
        }

        return $result;
    }

    public function getEmptyStateHeading(): ?string
    {
        $result = $this->evaluate($this->emptyStateHeading);

        if (! is_string($result)) {
            return null;
        }

        return $result;
    }

    public function getEmptyStateIcon(): ?string
    {
        $icon = $this->evaluate($this->emptyStateIcon) ?? FilamentIcon::resolve('tables::empty-state');

        if (! is_string($icon)) {
            return FilamentIcon::resolve('tables::empty-state');
        }

        return $icon;
    }

    public function hasEmptyState(): bool
    {
        return $this->emptyStateDescription || $this->emptyStateHeading;
    }
}
