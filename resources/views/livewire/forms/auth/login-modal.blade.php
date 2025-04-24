<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\Log;

new class extends Component
{
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        // Validierung wird wahrscheinlich durch das Form Object gehandhabt,
        // aber ein expliziter Aufruf hier schadet nicht, falls Logik geändert wird.
        // $this->validate(); // Prüfe, ob das LoginForm validate aufruft, ggf. hier entfernen.

        // Rufe die Authentifizierungsmethode des Form Objects auf.
        // Diese sollte bei Fehler eine ValidationException werfen.
        try {
            $this->form->authenticate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Fehler werden von Livewire automatisch angezeigt.
            // Verhindere, dass die Methode weiterläuft.
            return;
        }

        // Session regenerieren (wichtig nach Login)
        Session::regenerate();

        // !! ERSETZE Redirect durch Event !!
        $this->handleAuthSuccess();
    }

    /**
     * Handle the successful authentication.
     * Redirects to the dashboard.
     */
    public function handleAuthSuccess(): void
    {
        Log::info('welcome.navigation: Handling auth-successful, redirecting.'); // Log action
        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}; ?>

<div> {{-- Einfacher div-Wrapper statt Layout --}}
    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">
        Log in
    </h2>

    {{-- Zeige allgemeine Authentifizierungsfehler (z.B. von Throttling, auth.failed) --}}
    {{-- Das LoginForm muss diese Fehler ggf. setzen oder eine Exception werfen,
         die einen passenden Fehlerkey für @error unten produziert --}}
    @error('form.auth') {{-- Beispielhafter Key, prüfe das LoginForm --}}
    <div class="mb-4 p-3 text-sm text-red-700 bg-red-100 dark:bg-red-900/30 dark:text-red-300 rounded-md" role="alert">
        {{ $message }}
    </div>
    @enderror
    {{-- Session Status (optional im Modal) --}}
    {{-- <x-auth-session-status class="mb-4" :status="session('status')" /> --}}

    <form wire:submit="login">
        <div>
            <x-input-label for="modal-email" :value="__('Email')" /> {{-- Ggf. ID ändern wegen potentiellem Duplikat --}}
            <x-text-input wire:model="form.email" id="modal-email" class="block mt-1 w-full" type="email" name="email" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('form.email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="modal-password" :value="__('Password')" /> {{-- Ggf. ID ändern --}}
            <x-text-input wire:model="form.password" id="modal-password" class="block mt-1 w-full"
                type="password"
                name="password"
                required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('form.password')" class="mt-2" />
        </div>

        <div class="block mt-4">
            <label for="modal-remember" class="inline-flex items-center"> {{-- Ggf. ID ändern --}}
                <input wire:model="form.remember" id="modal-remember" type="checkbox" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800" name="remember">
                <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">{{ __('Remember me') }}</span>
            </label>
        </div>

        <div class="flex items-center justify-end mt-4">
            {{-- Passwort vergessen Link im Modal eher weglassen --}}
            {{-- @if (Route::has('password.request')) ... @endif --}}

            <x-primary-button class="ms-3" wire:loading.attr="disabled">
                <span wire:loading wire:target="login">{{ __('Logging in...') }}</span>
                <span wire:loading.remove wire:target="login">{{ __('Log in') }}</span>
            </x-primary-button>
        </div>
    </form>
</div>