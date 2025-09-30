@props([
    'user' => filament()->auth()->user(),
])

<div class="flex items-center space-x-2">
    <x-filament-panels::avatar.user :user="$user"/>
    <span class="text-sm">
        {{ $user->name }}
    </span>
</div>
