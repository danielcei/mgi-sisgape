<x-filament-panels::form wire:submit="authenticate">
    <style>
    .logo {
    display: flex;
    justify-content: center;
    margin-bottom: 16px;
    }

    .logo img.light {
    display: block;
    }

    .logo img.dark {
    display: none;
    }

    .dark .logo img.light {
    display: none;
    }

    .dark .logo img.dark {
    display: block;
    }
    </style>
    <div class="logo">
        <img src="{{ asset('images/logo.png') }}" alt="Logo" class="light">
        <img src="{{ asset('images/logo.png') }}" alt="Logo Dark" class="dark">
    </div>
    {{ $this->form }}
    <x-filament-panels::form.actions
        :actions="$this->getCachedFormActions()"
        :full-width="$this->hasFullWidthFormActions()"
    />
    <div class="logo-container">
    </div>
</x-filament-panels::form>
