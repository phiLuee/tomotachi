<?php

use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Title};

new
    #[Layout('layouts.app')]
    #[Title('Home')]
    class extends Component {}; ?>

<div class="bg-gradient-to-br from-blue-50 via-white to-pink-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 min-h-screen">
    <div class="relative min-h-screen flex flex-col items-center justify-center">
        <div class="relative w-full max-w-2xl px-6 lg:max-w-5xl">
            {{-- Header --}}
            <header class="flex flex-col items-center py-12">
                <div class="mb-4">
                    <img src="/logo.svg" alt="Logo" class="h-16 w-16 rounded-full shadow-lg bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700" />
                </div>
                <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 dark:text-white tracking-tight mb-2">
                    Willkommen bei <span class="text-blue-600 dark:text-blue-400">SocialConnect</span>
                </h1>
                <p class="text-gray-600 dark:text-gray-300 text-lg text-center max-w-xl">
                    Teile Momente, folge Freunden und entdecke neue Communities.<br>
                    Deine Plattform für Austausch, Inspiration und Spaß.
                </p>
                <div class="mt-6 flex gap-4">
                    <button
                        type="button"
                        class="px-6 py-2 rounded-lg bg-blue-600 text-white font-semibold shadow hover:bg-blue-700 transition"
                        wire:click="$dispatch('open-modal', { component: 'forms.auth.login-modal' })"
                    >
                        Login
                    </button>
                    <button
                        type="button"
                        class="px-6 py-2 rounded-lg bg-white text-blue-600 font-semibold border border-blue-600 shadow hover:bg-blue-50 dark:bg-gray-900 dark:text-blue-400 dark:border-blue-400 transition"
                        wire:click="$dispatch('open-modal', { component: 'forms.auth.register-modal' })"
                    >
                        Registrieren
                    </button>
                </div>
            </header>

            {{-- Hauptinhalt --}}
            <main class="mt-10">
                <div class="grid gap-8 md:grid-cols-2">
                    <div class="p-6 bg-white/80 dark:bg-gray-800/70 rounded-xl shadow-lg flex flex-col items-center">
                        <h2 class="text-xl font-bold text-blue-700 dark:text-blue-300 mb-2">Erstelle dein Profil</h2>
                        <p class="text-gray-600 dark:text-gray-300 text-center mb-4">
                            Präsentiere dich, lade ein Profilbild hoch und teile deine Interessen.
                        </p>
                        <img src="https://images.unsplash.com/photo-1519125323398-675f0ddb6308?auto=format&fit=crop&w=400&q=80"
                             alt="Profil erstellen" class="rounded-lg shadow w-40 h-32 object-cover" />
                    </div>
                    <div class="p-6 bg-white/80 dark:bg-gray-800/70 rounded-xl shadow-lg flex flex-col items-center">
                        <h2 class="text-xl font-bold text-pink-700 dark:text-pink-300 mb-2">Entdecke Beiträge</h2>
                        <p class="text-gray-600 dark:text-gray-300 text-center mb-4">
                            Sieh dir Posts von anderen an, like, kommentiere und folge spannenden Leuten.
                        </p>
                        <img src="https://images.unsplash.com/photo-1465101046530-73398c7f28ca?auto=format&fit=crop&w=400&q=80"
                             alt="Beiträge entdecken" class="rounded-lg shadow w-40 h-32 object-cover" />
                    </div>
                </div>
            </main>

            <footer class="py-12 text-center text-xs text-gray-500 dark:text-gray-400 mt-12">
                &copy; {{ date('Y') }} SocialConnect &middot; Laravel v{{ Illuminate\Foundation\Application::VERSION }} (PHP v{{ PHP_VERSION }})
            </footer>
        </div>
    </div>
</div>