<?php

namespace App\Forms\Components;

use Closure;
use Filament\Forms\Components\Field;
use Filament\Forms\Get;
use ReflectionFunction;

class RangeField extends Field
{
    protected string $view = 'forms.components.range-field';

    protected int|float|Closure|null $min = null;
    protected int|float|Closure|null $max = null;
    protected int|float|null $step = 1;

    public function min(int|float|Closure $value): static
    {
        $this->min = $value;

        return $this;
    }

    public function getMin(): int|float|null
    {
        return $this->evaluate($this->min);
    }

    public function getMinFormated(): string
    {
        return $this->formatPrice($this->evaluate($this->min));
    }


    public function max(int|float|Closure $value): static
    {
        $this->max = $value;

        return $this;
    }

    public function getMax(): int|float|null
    {
        return $this->evaluate($this->max);
    }

    public function getMaxFormated(): string
    {
        return $this->formatPrice($this->evaluate($this->max));
    }

    public function step(int|float $value): static
    {
        $this->step = $value;

        return $this;
    }

    public function getStep(): int|float|null
    {
        return $this->step;
    }

    public function evaluate(mixed $value, array $namedInjections = [], array $typedInjections = []): mixed
    {
        if (!$value instanceof Closure) {
            return $value;
        }

        $dependencies = [];

        foreach ((new ReflectionFunction($value))->getParameters() as $parameter) {
            $dependencies[] = $this->resolveClosureDependencyForEvaluation($parameter, $namedInjections, $typedInjections);
        }

        return $value(...$dependencies);
    }

    public function formatPrice($value): string
    {
        $value = preg_replace('/(\d+)\.(\d{2})$/', '$1,$2', str_replace(',', '', $value));
        return number_format((float)str_replace(',', '.', $value), 2, ',', '.');
    }

    public function parsePrice($value): float
    {
        return (float)str_replace(['.', ','], ['', '.'], $value);
    }
}
