<div class="flex justify-center">
    <div class="flex flex-col items-center justify-center space-y-4 px-6 py-12 text-center">
        <div class="rounded-full bg-gray-100 p-3 dark:bg-gray-500/20">
            <x-filament::icon
                :icon="$getEmptyStateIcon()"
                class="h-6 w-6 text-gray-500"
            />
        </div>

        <p class="text-lg font-medium text-gray-950 dark:text-white">
            {{ $getEmptyStateHeading() }}
        </p>

        <div class="max-w-md text-sm text-gray-500 dark:text-gray-400">
            {{ $getEmptyStateDescription() }}
        </div>
    </div>
</div>
