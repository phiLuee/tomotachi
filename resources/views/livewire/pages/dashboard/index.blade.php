<?php

declare(strict_types=1);

namespace App\Livewire\Pages\Dashboard;

use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Title, On}; // On hinzufügen

new
    #[Layout('layouts.app')] 
    #[Title('Dashboard')] 
    class extends Component {
        #[On('post-created')]
        public function closeCreatePostModal(): void
        {
            // Sende das Schließen-Event an den ModalManager
            $this->dispatch('close-modal'); 
        }

    }; ?> 

<div>
    {{-- Definiert den Header-Slot für das app.blade.php Layout --}}
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Responsive Grid-Layout --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                {{-- Hauptspalte (Button + Timeline) --}}
                <div class="md:col-span-2 space-y-6 order-last md:order-none">

                    {{-- Button zum Öffnen des CreatePost Modals --}}
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-4 sm:p-6"> {{-- Padding angepasst --}}
                            <button
                                type="button"
                                {{-- Sendet Event an den ModalManager --}}
                                wire:click="$dispatch('open-modal', { component: 'components.createpost' })"
                                class="w-full inline-flex items-center justify-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150"
                            >
                                Was gibt's Neues? {{-- Oder "Neuen Post erstellen" --}}
                            </button>
                        </div>
                    </div>

                    {{-- Timeline Komponente --}}
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                            <livewire:components.timeline />
                        </div>
                    </div>
                    {{-- Hier könnten weitere Elemente der Hauptspalte hin --}}
                </div>

                {{-- Seitenleiste (Widgets) --}}
                {{-- CreatePost wurde hier entfernt --}}
                <div class="md:col-span-1 space-y-6 order-first md:order-none">
                    {{-- Platzhalter für zukünftige Widgets in der Seitenleiste --}}
                    {{-- <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg"> ... </div> --}}
                </div>

            </div>
        </div>
    </div>
</div>