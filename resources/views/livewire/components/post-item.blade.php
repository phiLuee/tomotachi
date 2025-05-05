<?php

declare(strict_types=1);

namespace App\Livewire\Components;

use App\Models\Post;
use Livewire\Volt\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate; 
use Livewire\Attributes\On;

new class extends Component {
    public Post $post;
    public bool $isEditing = false;

    #[Validate('required|string|max:1000')]
    public string $editedContent = '';

    /**
     * Initialisiert die Komponente mit dem Post.
     */
    public function toggleLike(): void
    {
        if (!Auth::check()) {
            $this->dispatch('notify', 'Bitte einloggen, um Posts zu liken.', 'info');
            return;
        }

        $user = Auth::user();

        // Schaltet den Like-Status um
        $user->likedPosts()->toggle($this->post->id);

        // Aktualisiere die Anzahl der Likes und den Status
        $this->post->loadCount('likers'); 
        $this->post->setAttribute(
            'is_liked_by_current_user',
            $this->post->likers()->where('user_id', Auth::id())
            ->exists()
    );

    }

    /**
     * Startet den Bearbeitungsmodus.
     */
    public function startEditing(): void
    {
        if (Auth::id() !== $this->post->user_id || !$this->post->created_at->gt(now()->subMinutes(15))) {
            $this->dispatch('notify', 'Bearbeitung nicht erlaubt.', 'error');
            return;
        }
        $this->editedContent = $this->post->content; // Aktuellen Inhalt laden
        $this->isEditing = true;
    }

    /**
     * Bricht die Bearbeitung ab.
     */
    public function cancelEditing(): void
    {
        $this->isEditing = false;
        $this->resetValidation(); // Validierungsfehler zurücksetzen
    }

    /**
     * Speichert die Änderungen.
     */
    public function saveEdit(): void
    {
        if (Auth::id() !== $this->post->user_id) {
             $this->dispatch('notify', 'Nicht autorisiert.', 'error');
             $this->cancelEditing(); // Bearbeitung abbrechen
            return;
        }

        $validated = $this->validate();
        $this->post->update([
            'content' => $validated['editedContent'],
        ]);


        $this->isEditing = false;
        $this->resetValidation();

        $this->dispatch('notify', 'Post erfolgreich aktualisiert!');
    }
} ?>

{{-- Container für den einzelnen Post --}}
<div id="post-{{ $this->post->id }}" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border border-gray-200 dark:border-gray-700">
    {{-- User-Info & Zeitstempel --}}
    <div class="flex items-center mb-4">
        <div>
            <a href="{{ route('profile', ['username' => $this->post->user->username]) }}" class="font-semibold text-gray-900 dark:text-gray-100 hover:underline">
               {{ $this->post->user->name ?? 'Unknown User' }}
            </a>
            <div class="text-sm text-gray-500 dark:text-gray-400">
                {{ $this->post->created_at->diffForHumans() }}
            </div>
        </div>
    </div>

    {{-- Post-Inhalt --}}
    @if ($isEditing)
        {{-- Post-Inhalt (Edit-Mode) --}}
        <div class="mt-2">
            <textarea
                wire:model="editedContent"
                rows="3"
                class="block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
                placeholder="Bearbeite deinen Post..."
            ></textarea>
            {{-- Validierungsfehler anzeigen --}}
            @error('editedContent') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror

            <div class="mt-3 flex justify-end space-x-2">
                <button wire:click="saveEdit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm">Speichern</button>
                <button wire:click="cancelEditing" class="px-4 py-2 bg-gray-300 dark:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-md hover:bg-gray-400 dark:hover:bg-gray-500 text-sm">Abbrechen</button>
            </div>
        </div>
    @else
        {{-- Post-Inhalt (Normalansicht) --}}
        <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap mb-4">
            {{ $this->post->content }}
        </p>
    @endif

    {{-- Like-Button und Zähler --}}
    <div class="mt-4 flex items-center space-x-4">
        @auth
            <button 
                wire:click="toggleLike"
                @class(['flex items-center space-x-1 text-sm transition-colors duration-150 ease-in-out focus:outline-none', 'text-red-600 hover:text-red-700' => $this->post->is_liked_by_current_user, 'text-gray-500 hover:text-red-500 dark:text-gray-400 dark:hover:text-red-400' => !$this->post->is_liked_by_current_user])
                title="{{ $this->post->is_liked_by_current_user ? 'Unlike' : 'Like' }}"
            >
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5"> <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" /> </svg>
            </button>
        @else
            {{-- Statisches Icon für Gäste --}}
            <span class="flex items-center space-x-1 text-sm text-gray-500 dark:text-gray-400"> <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5"> <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" /> </svg> </span>
        @endauth
        <span class="text-sm text-gray-500 dark:text-gray-400">
            {{ $this->post->likers_count }} {{ Str::plural('Like', $this->post->likers_count) }}
        </span>
    </div>

    {{-- Aktionsbuttons (Bearbeiten/Löschen) - Nur anzeigen, wenn NICHT bearbeitet wird --}}
    @if (!$isEditing)
        @auth
            @if (Auth::id() === $this->post->user_id)
                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700 flex items-center space-x-3">
                    @if ($this->post->created_at->gt(now()->subMinutes(15)))
                        <button
                            wire:click="startEditing" {{-- Ruft jetzt die lokale Methode auf --}}
                            class="text-sm text-blue-600 dark:text-blue-400 hover:underline focus:outline-none" title="Bearbeiten">Bearbeiten</button>
                    @else
                         <span class="text-sm text-gray-400 dark:text-gray-500 cursor-not-allowed" title="Bearbeitung nicht mehr möglich">Bearbeiten</span>
                    @endif
                    <button
                        wire:click="$dispatch('confirm-delete', { postId: {{ $this->post->id }} })" {{-- Event nach oben senden --}}
                        wire:confirm="Möchtest du diesen Post wirklich löschen?"
                        class="text-sm text-red-600 dark:text-red-400 hover:underline focus:outline-none" title="Löschen">Löschen</button>
                </div>
            @endif
        @endauth
    @endif {{-- Ende von !$isEditing --}}
</div>