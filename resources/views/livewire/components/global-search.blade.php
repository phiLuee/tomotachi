<?php

declare(strict_types=1);

namespace App\Livewire\Components;

use App\Models\User;
use Livewire\Volt\Component;
use Illuminate\Database\Eloquent\Collection;

new class extends Component
{
    public string $searchQuery = '';
    public array $users = [];

    /**
     * Initializes the component.
     * Sets the initial state of the users collection.
     */
    public function mount(): void
    {
        $this->users = [];
    }

    /**
     * Updates the search query and fetches users based on the query.
     * The search is triggered when the user types in the search input.
     *
     * @param string $query The search query entered by the user.
     */
    public function updatedSearchQuery(string $query): void
    {
        $trimmedQuery = trim($query);

        if (strlen($trimmedQuery) >= 3) {
            $this->users = User::where('username', 'like', '%' . $trimmedQuery . '%')
                ->orWhere('name', 'like', '%' . $trimmedQuery . '%')
                ->select('id', 'username', 'name', 'profile_image')
                ->take(5)
                ->get()
                ->toArray();
        } else {
            $this->users = [];
        }
    }

    /**
     * Clears the search query and resets the users collection.
     * This is called when the user clicks outside the search input or presses Escape.
     */
    public function clearSearch(): void
    {
        $this->searchQuery = '';
        $this->users = [];
    }
} ?>

{{-- Blade-Teil der Komponente --}}
<div class="relative" x-data="{ open: false }" @click.away="open = false" wire:ignore.self>
    {{-- Suchfeld --}}
    <input
        type="text"
        wire:model.live.debounce.300ms="searchQuery"
        wire:keydown.escape="clearSearch(); open = false"
        @focus="open = true"
        placeholder="Suchen..."
        class="px-3 py-1.5 w-full sm:w-auto border border-gray-300 dark:border-gray-600 rounded-md text-sm dark:bg-gray-700 dark:text-gray-200 focus:ring-blue-500 focus:border-blue-500"
    />

    {{-- Container für Dropdown-Inhalt --}}
    {{-- ÄNDERUNG HIER: Füge $wire.searchQuery.length >= 3 zur x-show Bedingung hinzu --}}
    <div
        x-show="open && $wire.searchQuery.length >= 3"
        x-transition:enter="transition ease-out duration-100"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-75"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="absolute top-full mt-1 w-64 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-md shadow-lg z-50 max-h-60 overflow-y-auto"
        style="display: none;" {{-- Verhindert Flackern beim Laden --}}
    >
        {{-- Ergebnisliste (nur anzeigen, wenn Ergebnisse vorhanden) --}}
        {{-- Die Bedingung $wire.searchQuery.length >= 3 ist hier implizit durch die äußere div abgedeckt --}}
        <ul x-show="$wire.users.length > 0" class="divide-y divide-gray-100 dark:divide-gray-700">
            @forelse($users as $user)
                <li>
                    <a href="{{ route('profile', ['username' => $user['username']]) }}"
                       wire:click="clearSearch"
                       @click="open = false"
                       wire:navigate
                       class="flex items-center px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
                        <img src="{{ $user['profile_image'] ?? 'https://ui-avatars.com/api/?name=' . urlencode($user['username']) . '&background=random' }}"
                             alt="{{ $user['username'] }}" class="w-6 h-6 rounded-full mr-2 object-cover flex-shrink-0">
                        <span class="text-sm text-gray-700 dark:text-gray-200 truncate">{{ $user['name'] ?? $user['username'] }}</span>
                    </a>
                </li>
            @empty
                {{-- Wird nicht angezeigt, wenn x-show oben greift --}}
            @endforelse
        </ul>

         {{-- "Keine Ergebnisse"-Nachricht --}}
         {{-- Die Bedingung $wire.searchQuery.length >= 3 ist hier implizit durch die äußere div abgedeckt --}}
         {{-- Die PHP-Logik stellt sicher, dass $users leer ist, wenn die Query < 3 ist. --}}
         {{-- Wir müssen hier also nur prüfen, ob $users leer ist, WÄHREND das Dropdown (wegen Query >=3) offen ist. --}}
         <div x-show="$wire.users.length === 0" class="p-4">
            <p class="text-sm text-gray-500 dark:text-gray-400">Keine Benutzer gefunden.</p>
         </div>
    </div>
</div>