<?php

use Livewire\Volt\Component;
use Livewire\Attributes\{On, Computed};
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;


new class extends Component
{
    public const openModalEvent = 'open-modal';
    public const closeModalEvent = 'close-modal';

    public bool $showModal = false;
    public ?string $activeModalComponent = null;
    public array $modalComponentData = [];

    #[On('open-modal')]
    public function openModal(string $component = '', array $data = []): void
    {
        Log::info('ModalManager: openModal received.', ['component' => $component]);

        
        if($this->activeModalComponent !== $component) {
            $this->resetState();
        }

        // Aktive Komponente und Daten setzen
        $this->activeModalComponent = $component;
        $this->modalComponentData = $data;
        Log::info('ModalManager: State after setting component.', ['active' => $this->activeModalComponent]);

        // Modal anzeigen -> Löst Update aus
        $this->showModal = true;
        Log::info('ModalManager: Set showModal=true. Final state before update:', [
            'show' => $this->showModal,
            'active' => $this->activeModalComponent,
        ]);
    }

    #[On('close-modal')]
    public function requestClose(): void
    {
        $this->showModal = false;
    }

    /**
     * Setzt den Komponenten-Zustand zurück, wenn das Modal geschlossen ist.
     */
    public function resetState(): void
    {
        // Wenn das Modal noch geöffnet ist, den Reset abbrechen
        if ($this->showModal) {
            Log::info('ModalManager: resetState aborted, modal is open.');
            return;
        }
        $this->activeModalComponent = null;
        $this->modalComponentData = [];
        Log::info('ModalManager: state reset.');
    }

    /**
     * Computes the properties to pass to the dynamic component.
     * Converts snake_case keys from $modalComponentData to camelCase keys.
     *
     * @return array<string, mixed>
     */
    #[Computed]
    public function componentProps(): array
    {
        if (empty($this->modalComponentData)) {
            return []; // Always return an array
        }

        $props = [];
        foreach ($this->modalComponentData as $key => $value) {
            // Livewire typically maps kebab-case attributes to camelCase props,
            // but passing camelCase keys directly from here is cleaner.
            $props[Str::camel($key)] = $value;
        }
        Log::debug('ModalManager: Computed component props.', ['props' => array_keys($props)]);
        return $props;
    }
};
?>


{{-- Blade / HTML Teil --}}
<div>
    <div
        x-data="{ show: @entangle('showModal') }"
        x-show="show"
        x-on:keydown.escape.window="show = false"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 overflow-y-auto" style="display: none;"
        aria-labelledby="modal-title" role="dialog" aria-modal="true"
    >
        <div class="flex items-end justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            {{-- Overlay --}}
            <div x-show="show" x-on:click="show = false"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-gray-500/75 transition-opacity dark:bg-gray-900/80" aria-hidden="true"></div>

            {{-- Zentrierungstrick --}}
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            {{-- Modal Panel --}}
            <div x-show="show"
                 x-trap.inert.noscroll="show"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6"
            >
                {{-- Dynamischer Inhalt --}}
                @if($activeModalComponent)
                    @livewire(
                        $activeModalComponent,
                        $this->componentProps,
                        key('modal-content-' . $activeModalComponent . '-' . md5(json_encode($this->componentProps))) {{-- Dynamic key --}}
                    )
                @else
                     <div class="text-center p-4 text-gray-500 dark:text-gray-400">
                        <p>NO COMPONENT</p>
                     </div>
                @endif

                {{-- Schließen-Button --}}
                <button type="button" @click="show = false" class="absolute top-3 right-3 text-gray-400 hover:text-gray-500 dark:text-gray-400 dark:hover:text-gray-300">
                     <span class="sr-only">Schließen</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>
