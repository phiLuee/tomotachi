<?php

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Volt\Component;

new class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        // Wichtig: $user Variable zuweisen, damit sie für Auth::login verfügbar ist
        $user = User::create($validated);
        event(new Registered($user));

        Auth::login($user);

        $this->dispatch('auth-successful');
    }
}; ?>

<div>
    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">
        Register
    </h2>

    <form wire:submit="register">
        <div>
            <x-input-label for="register-name" :value="__('Name')" /> 
            <x-text-input wire:model="name" id="register-name" class="block mt-1 w-full" type="text" name="name" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="register-email" :value="__('Email')" />
            <x-text-input wire:model="email" id="register-email" class="block mt-1 w-full" type="email" name="email" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="register-password" :value="__('Password')" />
            <x-text-input wire:model="password" id="register-password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="register-password_confirmation" :value="__('Confirm Password')" /> 
            <x-text-input wire:model="password_confirmation" id="register-password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            {{-- "Already registered?" Link:
                 - Entweder entfernen für einfacheres Modal.
                 - Oder: href="#" und wire:click="$dispatch('show-login-modal')" hinzufügen,
                   wobei die Eltern-Komponente auf 'show-login-modal' hören muss,
                   um das RegisterModal zu schließen und das LoginModal zu öffnen.
            --}}
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
               href="#"
               wire:click.prevent="$dispatch('openLoginModal')" {{-- Beispiel: Event für Eltern-Komponente --}}
               >
                {{ __('Already registered?') }}
            </a>

            {{-- Submit Button mit Loading State --}}
            <x-primary-button class="ms-4" wire:loading.attr="disabled">
                 <span wire:loading wire:target="register">{{ __('Registering...') }}</span>
                 <span wire:loading.remove wire:target="register">{{ __('Register') }}</span>
            </x-primary-button>
        </div>
    </form>
</div>