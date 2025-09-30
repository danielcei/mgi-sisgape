<footer class="flex flex-col md:flex-row items-center justify-center w-full gap-4 px-4 py-8 text-sm text-gray-400 dark:text-gray-300">
    <span class="font-bold">
        {{ config('app.name') }}
        {{ App\MPS::version() }}
    </span>
    <span class="font-medium">
        &copy; {{ date('Y') }} MGI
    </span>
</footer>
