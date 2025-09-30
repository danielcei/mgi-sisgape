@if (filament()->isSidebarCollapsibleOnDesktop())
<div
    x-show="!$store.sidebar.isOpen"
    class="hidden sm:block"
>
    @if ($homeUrl = filament()->getHomeUrl())
        <a {{ \Filament\Support\generate_href_html($homeUrl) }}>
            <x-filament-panels::logo />
        </a>
    @else
        <x-filament-panels::logo />
    @endif
</div>
@endif