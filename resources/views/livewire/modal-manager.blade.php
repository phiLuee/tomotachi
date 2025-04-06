<?php

use Livewire\Volt\Component;
use Livewire\Attributes\On;

// Definiert die Volt-Komponente
new class extends Component
{
    // Zustand: Ist irgendein Modal sichtbar?
    public bool $showModal = false;
    
    // Welche Komponente soll im Modal angezeigt werden (z.B. 'auth.login-modal')?
    public ?string $activeModalComponent = null;

    // Daten, die an die Komponente im Modal übergeben werden sollen
    public array $modalComponentData = [];

    /**
     * Hört auf das generische 'open-modal' Event.
     * Setzt die aktive Komponente, übergibt Daten und zeigt das Modal an.
     */
    #[On('open-modal')]
    public function openModal(string $component, array $data = []): void
    {
        $this->activeModalComponent = $component;
        $this->modalComponentData = $data;
        $this->showModal = true; // Zeigt das Modal (via @entangle)
    }

    /**
     * Schließt das Modal.
     * Kann durch Alpine (Escape, Click away via @entangle) oder durch Events getriggert werden.
     * Hier definieren wir es hauptsächlich, um es bei Bedarf explizit aufrufen zu können
     * oder auf spezifische Schließ-Events zu hören.
     */
    #[On('close-modal')] // Generischer Listener zum Schließen
    public function closeModal(): void
    {
        $this->showModal = false;
        // Optional: Verzögert zurücksetzen, damit der Inhalt nicht springt während der Transition
        // $this->js('setTimeout(() => { $wire.resetState() }, 300)'); // 300ms = Alpine transition duration
        // Wenn nicht verzögert:
        $this->resetState();
    }

    /**
      * Setzt den Komponenten-Zustand zurück (wird nach dem Schließen aufgerufen).
      */
     public function resetState(): void
     {
         $this->activeModalComponent = null;
         $this->modalComponentData = [];
     }


    /**
     * Hört auf das 'auth-successful' Event von Login/Register Modals.
     * Schließt das Modal. Die Weiterleitung erfolgt in der Komponente, die das Modal geöffnet hat.
     */
    #[On('auth-successful')]
    public function closeOnAuthSuccess(): void
    {
        $this->closeModal();
    }

    /**
     * Wird aufgerufen, wenn $showModal von Alpine (via @entangle) geändert wird.
     * Wenn es auf false gesetzt wird, resetten wir den State.
     */
     public function updatedShowModal(bool $value): void
     {
         if (!$value) {
             // Verzögert zurücksetzen, damit der Inhalt nicht springt während der Transition
             $this->js('setTimeout(() => { $wire.resetState() }, 300)'); // 300ms = Alpine transition duration
         }
     }

}; ?>

<div> {{-- Root-Element für die Livewire-Komponente --}}

    {{-- ############################################# --}}
    {{-- ## MODAL-CONTAINER (GENERISCH)           ## --}}
    {{-- ############################################# --}}
    <div
        x-data="{ show: @entangle('showModal') }" {{-- Alpine 'show' an Livewire '$showModal' binden --}}
        x-show="show"
        x-on:keydown.escape.window="show = false" {{-- Schließt bei ESC (setzt Livewire state via entangle) --}}
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
            <div x-show="show" x-on:click="show = false" {{-- Schließt bei Klick daneben --}}
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-gray-500/75 transition-opacity" aria-hidden="true"></div>

            {{-- Zentrierungstrick --}}
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            {{-- Modal Panel --}}
            <div x-show="show"
                 x-trap.inert.noscroll="show" {{-- Fokus-Trap & Scroll-Sperre --}}
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6"
            >
                {{-- Dynamischer Inhalt basierend auf $activeModalComponent --}}
                @if($activeModalComponent)
                    <livewire:dynamic-component
                        :is="$activeModalComponent"
                        {{-- Wichtiger Key, damit Livewire die Komponente korrekt identifiziert --}}
                        wire:key="'modal-content-'. $activeModalComponent"
                     />
                @endif

                

                {{-- Optional: Schließen-Button --}}
                {{-- @click="show = false" schließt es direkt via @entangle --}}

                {{-- Optionaler expliziter Schließen-Button im Panel --}}
                {{-- Alpine @click="show = false" schließt es direkt via @entangle --}}
                <button type="button" @click="show = false" class="absolute top-3 right-3 text-gray-400 hover:text-gray-500">
                     <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
        </div>
    </div>
    {{-- ############################################# --}}
    {{-- ## ENDE DES MODAL-CONTAINERS             ## --}}
    {{-- ############################################# --}}
</div>