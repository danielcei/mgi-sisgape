<?php

declare(strict_types=1);

namespace SafeDeploy\Filament\Icons;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Support\Facades\App;
use InvalidArgumentException;

abstract class IconSet implements Plugin
{
    protected string $currentStyle;

    protected string $defaultStyle;

    /**
     * @var string[]
     */
    protected array $forcedStyles = [];

    /**
     * @var array<string, string>
     */
    protected array $iconMap = [];

    /**
     * @var array<string, string>
     */
    protected array $overriddenAliases = [];

    /**
     * @var array<string, string>
     */
    protected array $overriddenIcons = [];

    protected string $pluginId;

    protected bool $shouldPrefixStyle = false;

    /**
     * @var string[]
     */
    protected array $styleMap = [];

    /**
     * @param  array<mixed>  $arguments
     */
    public function __call(string $name, array $arguments): static
    {
        if (! array_key_exists($name, $this->styleMap)) {
            throw new InvalidArgumentException("Style '{$name}' is not available for this icon set.");
        }

        $this->currentStyle = $name;

        return $this;
    }

    final public static function make(): static
    {
        /** @var static $instance */
        $instance = App::make(static::class);

        return $instance;
    }

    final public function boot(Panel $panel): void
    {
        static::registerIcons();
    }

    final public function getId(): string
    {
        return $this->pluginId;
    }

    /**
     * @param  string|string[]  $keys
     */
    final public function overrideStyleForAlias(array|string $keys, string $style): static
    {
        $this->setOverriddenStyle($keys, $style, 'aliases');

        return $this;
    }

    /**
     * @param  array<string,string>|string  $icons
     */
    final public function overrideStyleForIcon(array|string $icons, string $style): static
    {
        $this->setOverriddenStyle($icons, $style, 'icons');

        return $this;
    }

    final public function register(Panel $panel): void
    {
        //
    }

    final public function registerIcons(): void
    {
        $style = $this->currentStyle ?? $this->defaultStyle ?? $this->styleMap[0];

        $icons = collect($this->getIconMap())
            ->mapWithKeys(function (string $icon, string $key) use ($style) {
                $forcedStyle = $this->forcedStyles[$icon] ?? null;
                $chosenStyle = $forcedStyle ?? $style;

                $styleString = $this->overriddenAliases[$key]
                    ?? $this->overriddenIcons[$icon]
                    ?? $this->styleMap[$chosenStyle]
                    ?? '';

                return [$key => $this->shouldPrefixStyle
                    ? $styleString.$icon
                    : $icon.$styleString];
            })
            ->all();

        FilamentIcon::register($icons);
    }

    /**
     * @return array<string, string>
     */
    protected function getIconMap(): array
    {
        /** @var array<string, string> $configIcons */
        $configIcons = config('safe-deploy.icons', []);

        return array_merge($this->iconMap, $configIcons);
    }

    /**
     * @param  string|string[]  $items
     */
    private function setOverriddenStyle(array|string $items, string $style, string $type = 'aliases'): void
    {
        $items = is_array($items) ? $items : [$items];
        $overrideType = $type === 'aliases' ? 'overriddenAliases' : 'overriddenIcons';

        if (! array_key_exists($style, $this->styleMap)) {
            throw new InvalidArgumentException("Style '{$style}' is not available for this icon set.");
        }

        foreach ($items as $item) {
            $this->{$overrideType}[$item] = $this->styleMap[$style];
        }
    }
}
