import preset from './vendor/filament/support/tailwind.config.preset'

export default {
    presets: [preset],
    content: [
        './app/Filament/**/*.php',
        './resources/views/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
    ],
    safelist: [
        'bg-red-600', 'bg-green-600', 'bg-yellow-600', // e outras classes que você usa dinamicamente
    ]
}
