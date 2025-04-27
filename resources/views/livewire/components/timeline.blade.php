<?php

// Importiere notwendige Klassen am Anfang
use App\Models\Post; 
use Illuminate\Support\Facades\Auth; 
use Illuminate\Database\Eloquent\Collection; 
use Livewire\Volt\Component;
use Livewire\Attributes\Computed; 

// Definiere die Volt-Komponente
new class extends Component
{
    public ?int $userId = null;
    protected $listeners = ['post-created' => 'refreshPosts'];

    /**
     * Berechne die Posts für die Timeline.
     * #[Computed] sorgt dafür, dass das Ergebnis für die Dauer
     * eines Requests gecached wird, um unnötige Datenbankabfragen
     * bei mehreren Zugriffen im selben Render-Zyklus zu vermeiden.
     */
    #[Computed]
    public function posts(): Collection
    {
        // Starte den Query Builder
        $query = Post::query();

        // Prüfe, ob eine spezifische userId übergeben wurde
        if ($this->userId !== null) {
            // Filtere nur nach der übergebenen userId
            $query->where('user_id', $this->userId);
        } else {
            // Standardverhalten: Zeige Posts von gefolgten Usern und eigene Posts
            if (!Auth::check()) {
                return collect(); // Leere Collection zurückgeben, wenn nicht eingeloggt
            }

            $user = Auth::user();
            $followedUserIds = $user->following()->pluck('users.id');
            $followedUserIds->push($user->id); // Eigene ID hinzufügen

            $query->whereIn('user_id', $followedUserIds);
        }

        // Wende gemeinsame Bedingungen und Eager Loading an
        return $query->with('user:id,name,username') // Lade User-Infos effizient
                    ->withCount('likers') // Lade die Anzahl der Likes
                   ->latest() // Sortiere nach Datum
                   ->get(); // Hole die Ergebnisse
    }

    /**
     * Optional: Was soll passieren, wenn die Komponente zum ersten Mal geladen wird?
     * Kann leer bleiben, wenn die Logik in #[Computed] ist.
     */
    public function mount(?int $userId = null): void
    {
        $this->userId = $userId;
    }


    public function refreshPosts(): void
    {
        // Da 'posts' eine #[Computed] Property ist, müssen wir sie nicht
        // manuell neu laden. Livewire erkennt die Änderung und ruft
        // die computed Property beim nächsten Rendern automatisch neu auf.
        // Ein einfacher Trigger genügt oft, oder leere die Cache-Variable, falls manuell gecached.

        // Alternativ, wenn posts() KEINE computed property wäre:
        // $this->posts = $this->loadPosts(); // Beispiel: Lade Methode aufrufen

        // Manchmal reicht es auch, die Komponente einfach neu zu rendern:
        // (Keine Aktion hier nötig, da #[On] das oft schon triggert)

        // Optional: Nach oben scrollen, wenn ein neuer Post erstellt wurde
        // $this->js('window.scrollTo({ top: 0, behavior: "smooth" })');
    }

    public function confirmDelete(int $postId): void
    {
        $post = Post::findOrFail($postId);

        // Sicherheitscheck: Nur eigene Posts löschen
        if (Auth::id() !== $post->user_id) {
            // Optional: Fehlermeldung oder Event auslösen
             $this->dispatch('notify', 'Du kannst nur deine eigenen Posts löschen.', 'error');
            return;
        }

        $post->delete();

        // Computed Property Cache leeren, damit die Liste aktualisiert wird
        unset($this->posts);

        // Optional: Erfolgsmeldung
        $this->dispatch('notify', 'Post erfolgreich gelöscht!');
    }

        /**
     * Schaltet den Like-Status für einen Post für den eingeloggten Benutzer um.
     */
    public function toggleLike(int $postId): void
    {
        // Nur für eingeloggte Benutzer
        if (!Auth::check()) {
            // Optional: Redirect zum Login oder Fehlermeldung
            $this->dispatch('notify', 'Bitte einloggen, um Posts zu liken.', 'info');
            return;
        }

        $user = Auth::user(); 
        $post = Post::find($postId); // Finde den Post

        if (!$post) {
            // Post wurde möglicherweise inzwischen gelöscht
            $this->dispatch('notify', 'Post nicht gefunden.', 'error');
            return;
        }

        // Schaltet den Like-Status um:
        // - Fügt den Eintrag in der 'likes'-Tabelle hinzu, wenn er nicht existiert.
        // - Entfernt den Eintrag, wenn er existiert.
        $user->likedPosts()->toggle($postId);

        // Computed Property Cache leeren, damit die Liste (insb. Like-Count und Button-Status)
        // beim nächsten Rendern aktualisiert wird.
        unset($this->posts);

        // Optional: Event für UI-Updates oder Benachrichtigungen
        // $this->dispatch('notify', 'Like-Status geändert!');
    }

}; ?>

<div class="space-y-6"> {{-- Fügt vertikalen Abstand zwischen den Posts hinzu --}}

    {{-- Prüfe, ob Posts vorhanden sind, und loope durch sie hindurch --}}
    @forelse ($this->posts() as $post)
        {{-- Container für jeden einzelnen Post --}}
        <div id="post-{{ $post->id }}" wire:key="post-{{ $post->id }}" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 border border-gray-200 dark:border-gray-700">
            <div class="flex items-center mb-4">
                {{-- Optional: Avatar --}}
                {{-- <img src="{{ $post->user->avatar_url ?? '/default-avatar.png' }}" alt="{{ $post->user->name }}" class="h-10 w-10 rounded-full mr-3"> --}}

                <div>
                    {{-- User-Name (ggf. Link zum Profil) --}}
                    <a href="{{ route('profile.show', ['username' => $post->user->username]) }}" class="font-semibold text-gray-900 dark:text-gray-100 hover:underline">
                       {{ $post->user->name ?? 'Unknown User' }}
                    </a>
                    {{-- Optional: Username --}}
                    {{-- <span class="text-sm text-gray-500 dark:text-gray-400 ml-1">@ {{ $post->user->username ?? 'username' }}</span> --}}

                    {{-- Zeitstempel --}}
                    <div class="text-sm text-gray-500 dark:text-gray-400">
                        {{-- ->diffForHumans() gibt etwas wie "vor 5 Minuten" aus --}}
                        {{ $post->created_at->diffForHumans() }}
                    </div>
                </div>
            </div>

            {{-- Post-Inhalt --}}
            <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">
                {{ $post->content }}
            </p>

{{-- Like-Button und Zähler --}}
            <div class="mt-4 flex items-center space-x-4"> {{-- Container für Like-Aktion und Zähler --}}
                @auth {{-- Like-Button nur für eingeloggte User interaktiv --}}
                    <button
                        wire:click="toggleLike({{ $post->id }})"
                        @class([
                            'flex items-center space-x-1 text-sm transition-colors duration-150 ease-in-out focus:outline-none',
                            'text-red-600 hover:text-red-700' => $post->isLikedByCurrentUser(), // Rote Farbe, wenn geliked
                            'text-gray-500 hover:text-red-500 dark:text-gray-400 dark:hover:text-red-400' => !$post->isLikedByCurrentUser(), // Grau/Rot beim Hover, wenn nicht geliked
                        ])
                        title="{{ $post->isLikedByCurrentUser() ? 'Unlike' : 'Like' }}"
                    >
                        {{-- Herz-Icon (SVG Beispiel) --}}
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                          <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                        </svg>
                        {{-- Optional: Text wie "Like" / "Unlike" --}}
                        {{-- <span>{{ $post->isLikedByCurrentUser() ? 'Unlike' : 'Like' }}</span> --}}
                    </button>
                @else {{-- Für nicht eingeloggte User nur statische Anzeige --}}
                    <span class="flex items-center space-x-1 text-sm text-gray-500 dark:text-gray-400">
                         <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                          <path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd" />
                        </svg>
                    </span>
                @endauth

                {{-- Like-Zähler (immer sichtbar) --}}
                <span class="text-sm text-gray-500 dark:text-gray-400">
                    {{-- Verwende likers_count, das durch withCount geladen wird --}}
                    {{ $post->likers_count }} {{ Str::plural('Like', $post->likers_count) }}
                </span>
            </div>
             @auth
                @if (Auth::id() === $post->user_id)
                    <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700 flex items-center space-x-3"> {{-- Trennlinie und Abstand --}}

                        {{-- Bearbeiten-Button (nur wenn Post < 15 Minuten alt) --}}
                        @if ($post->created_at->gt(now()->subMinutes(15)))
                            <button
                                wire:click="startEditing({{ $post->id }})"
                                class="text-sm text-blue-600 dark:text-blue-400 hover:underline focus:outline-none"
                                title="Bearbeiten"
                            >
                                Bearbeiten
                            </button>
                        @else
                             {{-- Optional: Deaktivierten Button anzeigen oder ganz weglassen --}}
                             <span class="text-sm text-gray-400 dark:text-gray-500 cursor-not-allowed" title="Bearbeitung nicht mehr möglich">Bearbeiten</span>
                        @endif

                        {{-- Löschen-Button mit Bestätigung --}}
                        <button
                            wire:click="confirmDelete({{ $post->id }})"
                            wire:confirm="Möchtest du diesen Post wirklich löschen?"
                            class="text-sm text-red-600 dark:text-red-400 hover:underline focus:outline-none"
                            title="Löschen"
                        >
                            Löschen
                        </button>
                    </div>
                @endif
            @endauth
        </div>
    @empty
        {{-- Nachricht, wenn keine Posts vorhanden sind --}}
        <div class="text-center text-gray-500 dark:text-gray-400 py-8">
            <p>Noch keine Posts vorhanden.</p>
            <p class="mt-2">Folge anderen Nutzern, um ihre Posts hier zu sehen!</p>
            {{-- Oder wenn nur eigene Posts angezeigt werden: --}}
            {{-- <p>Du hast noch nichts gepostet.</p> --}}
        </div>
    @endforelse

    {{-- Optional: Hier könnten später Pagination-Links hin, wenn du ->paginate() statt ->get() verwendest --}}
    {{-- <div class="mt-4">
        {{ $this->posts()->links() }}
    </div> --}}

</div>