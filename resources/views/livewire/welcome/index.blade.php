<?php

use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Title};

new
    #[Layout('layouts.app')]
    #[Title('Welcome')]
    class extends Component {}; ?>

<div class="bg-gray-50 text-black/50 dark:bg-black dark:text-white/50">
    {{-- Optional: Hintergrundbild --}}
    {{-- <img id="background" class="absolute -left-20 top-0 max-w-[877px]" src="https://laravel.com/assets/img/welcome/background.svg" /> --}}

    <div class="relative min-h-screen flex flex-col items-center justify-center selection:bg-[#FF2D20] selection:text-white">
        <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl">
            <header class="grid grid-cols-2 items-center gap-2 py-10 lg:grid-cols-3">
                <div class="flex lg:justify-center lg:col-start-2">
                    {{-- Dein Logo oder Inhalt --}}
                </div>
                @if (Route::has('login'))
                {{-- !! Deine Welcome-Navigation Komponente !! --}}
                <livewire:welcome.navigation />
                @endif
            </header>

            <main class="mt-6">
                {{-- Dein Hauptinhalt der Welcome-Seite --}}
                <div class="grid gap-6 lg:grid-cols-2 lg:gap-8">
                    {{-- Beispielinhalt --}}
                    <div class="p-6 bg-white dark:bg-gray-800/50 dark:bg-gradient-to-bl from-gray-700/50 via-transparent rounded-lg shadow-2xl shadow-gray-500/20 dark:shadow-none">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Willkommen!</h2>
                        <p class="mt-4 text-gray-500 dark:text-gray-400 text-sm leading-relaxed">
                            Dies ist deine Landing Page. Klicke oben auf Login oder Register.
                        </p>
                    </div>
                </div>

            </main>

            <footer class="py-16 text-center text-sm text-black dark:text-white/70">
                Laravel v{{ Illuminate\Foundation\Application::VERSION }} (PHP v{{ PHP_VERSION }})
            </footer>
        </div>
    </div>