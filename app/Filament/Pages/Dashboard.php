<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\WelcomeMessage;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.dashboard';

    protected function getHeaderWidgets(): array
    {

        $widgets[] = WelcomeMessage::class;

        return $widgets;
    }
}
