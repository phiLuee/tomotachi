<?php

declare(strict_types=1);

namespace App\Livewire\Components;

use Livewire\Volt\Component;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Auth;

// Definiere die Volt-Komponente
new class extends Component
{
    // Validation-Regeln direkt an die Property binden
    #[Validate('required|min:3|max:280')] // Post muss vorhanden sein, min 3, max 280 Zeichen
    public string $content = ''; // Die Property, die an das Textarea gebunden wird

    /**
     * Speichert den neuen Post.
     * Wird durch wire:submit="save" im Formular aufgerufen.
     */
    public function save(): void
    {
        // Sicherstellen, dass der User eingeloggt ist (obwohl die Komponente wahrscheinlich nur dann angezeigt wird)
        if (!Auth::check()) {
            // Optional: Fehler werfen oder Weiterleiten
            session()->flash('error', 'Bitte zuerst einloggen.');
            $this->redirect(route('login'), navigate: true); // Nutzt Livewire's SPA-Navigation
            return;
        }

        // Eingabe validieren basierend auf den #[Validate] Regeln oben
        $validated = $this->validate();

        // Post erstellen und mit dem eingeloggten User verknüpfen
        // Annahme: Im User-Model gibt es eine 'posts()' HasMany-Relation
        Auth::user()->posts()->create([
            'content' => $validated['content'], // Verwende den validierten Inhalt
        ]);
 
        // Textfeld leeren nach erfolgreichem Speichern 
        $this->reset('content');
        // oder: $this->content = '';

        // Optional: Erfolg-Feedback geben (z.B. über ein Event oder eine Session-Nachricht)
        // -> Event ist oft besser, damit z.B. die Timeline sich aktualisieren kann
        $this->dispatch('post-created'); // Event aussenden

        // Optional: Session Flash Message für direktes Feedback
        session()->flash('status', 'Post erfolgreich erstellt!');
    }

}; ?>

{{-- Blade-Teil der Volt-Komponente --}}
<div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 border border-gray-200 dark:border-gray-700">
    {{-- Formular, das die 'save'-Methode bei Submit aufruft --}}
    <form wire:submit="save">
        {{-- Textarea für den Post-Inhalt --}}
        {{-- wire:model="content" bindet den Wert an die $content Property im PHP-Teil --}}
        <textarea
            wire:model="content"
            rows="3"
            class="w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm"
            placeholder="Was gibt's Neues?"></textarea>

        {{-- Anzeige von Validierungsfehlern für das 'content'-Feld --}}
        @error('content')
        <p class="text-sm text-red-600 dark:text-red-400 mt-2">{{ $message }}</p>
        @enderror

        {{-- Optional: Zeichenzähler --}}
        {{-- <div class="text-sm text-gray-500 dark:text-gray-400 mt-1 text-right">
            {{ strlen($content) }} / 280
</div> --}}

<div class="mt-4 flex justify-end">
    {{-- Submit-Button --}}
    {{-- wire:loading.attr="disabled" deaktiviert den Button während des Ladens --}}
    <button
        type="submit"
        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150"
        wire:loading.attr="disabled">
        {{-- Zeigt "Speichern..." an, während die Aktion läuft --}}
        <span wire:loading wire:target="save">Speichern...</span>
        {{-- Zeigt "Posten" an, wenn nicht geladen wird --}}
        <span wire:loading.remove wire:target="save">Posten</span>
    </button>
</div>
</form>

{{-- Optional: Direkte Erfolgsnachricht anzeigen --}}
{{-- @if (session('status'))
        <div class="mt-4 text-sm text-green-600 dark:text-green-400">
            {{ session('status') }}
</div>
@endif --}}
</div>