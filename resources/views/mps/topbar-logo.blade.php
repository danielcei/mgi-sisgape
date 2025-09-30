@php
    $brandName = filament()->getBrandName();
    $brandLogo = filament()->getBrandLogo();
    $brandLogoHeight = filament()->getBrandLogoHeight() ?? '1.5rem';
    $darkModeBrandLogo = filament()->getDarkModeBrandLogo();
    $hasDarkModeBrandLogo = filled($darkModeBrandLogo);

    $getLogoClasses = fn (bool $isDarkMode): string => \Illuminate\Support\Arr::toCssClasses([
        'fi-logo',
        'inline-flex' => ! $hasDarkModeBrandLogo,
        'inline-flex dark:hidden' => $hasDarkModeBrandLogo && (! $isDarkMode),
        'hidden dark:inline-flex' => $hasDarkModeBrandLogo && $isDarkMode,
    ]);

    $logoStyles = "height: {$brandLogoHeight}";
@endphp

@capture($content, $logo, $isDarkMode = false)
@if ($logo instanceof \Illuminate\Contracts\Support\Htmlable)
    {!! $logo !!}
@elseif (filled($logo))
    <img
        alt="{{ $brandName }}"
        loading="lazy"
        src="{{ $logo }}"
        style="{{ $logoStyles }}"
        class="{{ $getLogoClasses($isDarkMode) }}"
    />
@else
    <span class="{{ $getLogoClasses($isDarkMode) }}">
            {{ $brandName }} .....
        </span>
@endif
@endcapture

<!-- Render the logos for both themes -->
@if ($hasDarkModeBrandLogo)
    <div id="dark-logo" style="display: none;">
        {{ $content($darkModeBrandLogo, true) }}
    </div>
@endif

<div id="light-logo" style="display: none;">
    {{ $content($brandLogo) }}
</div>

<script
    src="https://js.sentry-cdn.com/aeed188db64e8f1b33593679b30fb8b5.min.js"
    crossorigin="anonymous"
></script>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const logoElement = document.querySelector('.fi-logo');
        const sidebarNavElement = document.querySelector('.fi-sidebar-nav');
        const sidebarHeaderElement = document.querySelector('.fi-sidebar-header');
        const darkLogo = document.getElementById('dark-logo').innerHTML;
        const lightLogo = document.getElementById('light-logo').innerHTML;

        if (!logoElement || !sidebarNavElement || !sidebarHeaderElement) {
            console.log('There are non default changes in DOM -> withLogoInTopbar not working');
            return;
        }

        @if ($isSidebarCollapsibleOnDesktop)
        logoElement.parentNode.parentNode.classList.add('md:hidden');
        sidebarNavElement.classList.add('pt-0');
        @else
        sidebarHeaderElement.classList.add('md:hidden');
        @endif

        logoElement.classList.add('justify-center');

        const insertLogo = (isDarkMode) => {
            const topbarEnd = document.querySelector('[x-persist="topbar.end"]');
            const logoHTML = isDarkMode ? darkLogo : lightLogo;

            if (topbarEnd) {
                const existingLogo = topbarEnd.previousElementSibling;
                if (existingLogo && existingLogo.classList.contains('topbar-logo')) {
                    existingLogo.innerHTML = logoHTML;
                } else {
                    topbarEnd.insertAdjacentHTML('beforebegin', `
                        <div class="w-full hidden md:block text-center topbar-logo">
                            ${logoHTML}
                        </div>
                    `);
                }
            }
        };

        const initialTheme = document.documentElement.classList.contains('dark');
        insertLogo(initialTheme);

        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.attributeName === 'class') {
                    const isDarkMode = document.documentElement.classList.contains('dark');
                    insertLogo(isDarkMode);
                }
            });
        });

        observer.observe(document.documentElement, { attributes: true });
    });
</script>
