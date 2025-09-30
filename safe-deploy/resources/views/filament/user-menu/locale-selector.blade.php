@php
    use Filament\Support\Facades\FilamentIcon;
    use Illuminate\Support\Facades\Cookie;
    use Illuminate\Support\Facades\Auth;

    $locales = config('app.locales', []);
    $current = Cookie::get('locale') ?? Auth::user()?->locale ?? config('app.locale');
@endphp

@if(count($locales) > 1)
    <x-filament::dropdown.list>
        <x-filament::dropdown placement="left-start">
            <x-slot name="trigger">
                <x-filament::dropdown.list.item :icon="FilamentIcon::resolve('safe-deploy::locale')">
                    {{ $locales[$current] }}
                </x-filament::dropdown.list.item>
            </x-slot>

            <x-filament::dropdown.list>
                @foreach($locales as $code => $label)
                    @if($code !== $current)
                        <x-filament::dropdown.list.item
                            :action="route('safe-deploy.set-locale')"
                            method="post"
                            tag="form"
                        >
                            <input type="hidden" name="locale" value="{{ $code }}"/>
                            {{ $label }}
                        </x-filament::dropdown.list.item>
                    @endif
                @endforeach
            </x-filament::dropdown.list>
        </x-filament::dropdown>
    </x-filament::dropdown.list>
@endif
