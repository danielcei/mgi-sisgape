@php
    use \SafeDeploy\Support\Enums\Event;
    use \SafeDeploy\Support\Enums\Field;
@endphp
<span class="flex items-center gap-x-2">
    <x-filament::badge size="sm" icon="{{ Field::ID->getIcon() }}" color="gray">
        {{ $id }}
    </x-filament::badge>
    <x-filament::badge size="sm" icon="{{ Event::CREATED->getIcon() }}" color="gray">
        {{ $createdSince }}
        @isset($createdBy)
            @lang('by')
            <a href="{{ $createdByUrl }}" wire:navigate
               class="text-primary-600 dark:text-primary-400">{{ $createdBy }}</a>
        @endisset
    </x-filament::badge>
    @isset($updatedSince)
        <x-filament::badge size="sm" icon="{{ Event::UPDATED->getIcon() }}" color="gray">
        {{ $updatedSince }}
            @isset($updatedBy)
                @lang('by')
                <a href="{{ $updatedByUrl }}" wire:navigate
                   class="text-primary-600 dark:text-primary-400">{{ $updatedBy }}</a>
            @endisset
    </x-filament::badge>
    @endif
</span>
