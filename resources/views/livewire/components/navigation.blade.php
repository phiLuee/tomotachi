<?php

declare(strict_types=1);

namespace App\Livewire\Components;


use Livewire\Volt\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Log;

new class extends Component
{
    /**
     * Öffnet das Login-Modal durch Dispatch des generischen Events.
     */
    public function openLoginModal(): void
    {
        Log::info('welcome.navigation: Dispatching open-modal for login using named param.'); // Log dispatch
        // Sende generisches Event mit Komponenten-Alias an den ModalManager
        // KORREKTUR: Benannte Argumente wieder verwenden
        $this->dispatch('open-modal', component: 'forms.auth.login-modal');
    }

    /**
     * Öffnet das Registrierungs-Modal durch Dispatch des generischen Events.
     */
    public function openRegisterModal(): void
    {
        Log::info('welcome.navigation: Dispatching open-modal for register using named param.'); // Log dispatch
        // Sende generisches Event mit Komponenten-Alias an den ModalManager
        // KORREKTUR: Benannte Argumente wieder verwenden
        $this->dispatch('open-modal', component: 'forms.auth.register-modal');
    }
} ?>
 
{{-- Der HTML/Blade-Teil der Komponente (unverändert) --}}
<nav class="-mx-3 flex flex-1 justify-end">
    @auth
    <a href="{{ route('dashboard', absolute: false) }}" wire:navigate class="...">Dashboard</a> {{-- Klassen anpassen --}}
    @else
    {{-- Button ruft openLoginModal in DIESER Komponente auf --}}
    <button type="button" wire:click="openLoginModal" class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white">
        Log in
    </button>

    @if (Route::has('register'))
    {{-- Button ruft openRegisterModal in DIESER Komponente auf --}}
    <button type="button" wire:click="openRegisterModal" class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white ml-4"> {{-- ml-4 für Abstand hinzugefügt --}}
        Register
    </button>
    @endif
    @endauth
</nav>