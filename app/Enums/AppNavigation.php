<?php

declare(strict_types=1);

namespace App\Enums;

use Filament\Support\Facades\FilamentIcon;
use Illuminate\Support\Collection;
use SafeDeploy\Filament\Contracts\AppNavigationGroup;

enum AppNavigation: string implements AppNavigationGroup
{
    case ANALISE = 'analise';
    case ADMINISTRATIVO = 'administrativo';
    case ASSOCIADO = 'associado';
    case FATURAMENTO = 'faturamento';
    case VENDA = 'venda';
    case SIMULADOR = 'simulador';
    case FUNCOES_E_PERMISSOES = 'funcoes-e-permissoes';

    public static function byLabel(string $label): ?static
    {
        return Collection::make(self::cases())
            ->first(fn (self $case): bool => $case->getLabel() === $label);
    }

    /**
     * Remember to add the case keys to the scss $navigation var in {@see /resources/css/filament/safe-deploy-icon/theme.scss}
     */
    public function getColor(): string
    {
        return "nav-color-$this->value";
    }

    public function getColorHex(): string
    {
        return match ($this) {
            self::ADMINISTRATIVO => '#FF1F57',
            self::ASSOCIADO => '#FE9900',
            self::FATURAMENTO => '#62568F',
            self::VENDA => '#006CAD',
            self::SIMULADOR => '#31925F',
            self::FUNCOES_E_PERMISSOES => '#666666',
            self::ANALISE => '#4081B8',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::ANALISE => FilamentIcon::resolve('safe-deploy-icon::group.analise'),
            self::ADMINISTRATIVO => FilamentIcon::resolve('safe-deploy-icon::group.administrativo'),
            self::ASSOCIADO => FilamentIcon::resolve('safe-deploy-icon::group.associado'),
            self::FATURAMENTO => FilamentIcon::resolve('safe-deploy-icon::group.faturamento'),
            self::VENDA => FilamentIcon::resolve('safe-deploy-icon::group.venda'),
            self::SIMULADOR => FilamentIcon::resolve('safe-deploy-icon::group.simulador'),
            self::FUNCOES_E_PERMISSOES => FilamentIcon::resolve('safe-deploy-icon::group.funcoes-e-permissoes'),
        };
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::ANALISE => __('Analise'),
            self::ADMINISTRATIVO => __('Administrativo'),
            self::ASSOCIADO => __('Associado'),
            self::FATURAMENTO => __('Faturamento'),
            self::VENDA => __('Venda'),
            self::SIMULADOR => __('Simulador'),
            self::FUNCOES_E_PERMISSOES => __('Funções e Permissões'),
        };
    }

    public function isCollapsedByDefault(): bool
    {
        return match ($this) {
            self::FUNCOES_E_PERMISSOES => true,
            default => false,
        };
    }
}
