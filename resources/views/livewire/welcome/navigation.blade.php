<?php

namespace App\Livewire\Welcome;

use Livewire\Volt\Component;
use Livewire\Attributes\On;

new class extends Component
{
    /**
     * @var string[]
     */
    protected $listeners = [
        'auth-successful' => 'handleAuthSuccess',
        'openLoginModal' => 'switchToLoginModal',
        'openRegisterModal' => 'switchToRegisterModal',
    ];

    
    public function openLoginModal(): void
    {
        // Sende generisches Event mit Komponenten-Alias
        $this->dispatch('open-modal', component: 'auth.login-modal');
    }

    public function openRegisterModal(): void
    {
         // Sende generisches Event mit Komponenten-Alias
        $this->dispatch('open-modal', component: 'auth.register-modal');
    }

    #[On('auth-successful')]
    public function handleAuthSuccess(): void
    {
        // Der ModalManager schließt sich selbst via Listener.
        // Diese Komponente leitet weiter.
        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }


     #[On('openLoginModal')] // Kommt vom Register-Link
     public function switchToLoginModal(): void
     {
         $this->openLoginModal(); // Öffnet das Login-Modal via Event
     }

     #[On('openRegisterModal')] // Kommt vom Login-Link
     public function switchToRegisterModal(): void
     {
         $this->openRegisterModal(); // Öffnet das Register-Modal via Event
     }


}
?>

<nav class="-mx-3 flex flex-1 justify-end">
    @auth
        <a href="{{ route('dashboard', absolute: false) }}" wire:navigate class="...">Dashboard</a>
    @else
        <button type="button" wire:click="openLoginModal" class="p-6">Log in</button>
        @if (Route::has('register'))
            <button type="button" wire:click="openRegisterModal" class="p-6">Register</button>
        @endif
    @endauth
</nav>
