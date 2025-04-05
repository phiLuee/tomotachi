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
        // Sicherstellen, dass der User eingeloggt ist
        if (!Auth::check()) {
            return collect(); // Leere Collection zurückgeben, wenn nicht eingeloggt
        }

        $user = Auth::user();

        // Annahme: Du hast eine 'following'-Relation im User-Model definiert,
        // die die User zurückgibt, denen der aktuelle User folgt.
        // Beispiel: return $this->belongsToMany(User::class, 'follows', 'follower_id', 'following_id');
        $followedUserIds = $user->following()->pluck('users.id'); // IDs der gefolgten User holen

        // Füge die ID des aktuellen Users hinzu, um auch eigene Posts zu sehen
        $followedUserIds->push($user->id);

        // Hole die Posts der gefolgten User und des eigenen Users
        return Post::whereIn('user_id', $followedUserIds) // Nur Posts dieser User
                   ->with('user:id,name,username') // Eager Loading: Lade den zugehörigen User mit (nur relevante Spalten)
                   ->latest() // Sortiere nach Datum, neueste zuerst
                   ->get(); // Hole die Ergebnisse als Collection
    }

    /**
     * Optional: Was soll passieren, wenn die Komponente zum ersten Mal geladen wird?
     * Kann leer bleiben, wenn die Logik in #[Computed] ist.
     */
    public function mount(): void
    {
        // Hier könntest du initiale Dinge tun, falls nötig.
        // Oft ist es aber sauberer, die Daten über #[Computed] zu laden.
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
                    <a href="#" class="font-semibold text-gray-900 dark:text-gray-100 hover:underline">
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

            {{-- Optional: Platz für Aktionen wie Like, Kommentar etc. --}}
            {{-- <div class="mt-4 flex space-x-4">
                <button wire:click="toggleLike({{ $post->id }})" class="text-gray-500 hover:text-blue-600">Like</button>
                <button class="text-gray-500 hover:text-green-600">Comment</button>
            </div> --}}
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