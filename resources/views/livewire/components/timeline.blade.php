<?php
declare(strict_types=1);

use App\Models\Post; 
use Illuminate\Support\Facades\Auth; 
use Illuminate\Database\Eloquent\Collection; 
use Livewire\Volt\Component;
use Livewire\Attributes\Computed; 
use Livewire\Attributes\On;

new class extends Component
{
    public ?int $userId = null;

    /**
     * Berechne die Posts für die Timeline.
     * #[Computed] sorgt dafür, dass das Ergebnis für die Dauer
     * eines Requests gecached wird, um unnötige Datenbankabfragen
     * bei mehreren Zugriffen im selben Render-Zyklus zu vermeiden.
     */
    #[Computed]
    public function posts(): Collection
    {
        logger("--> posts() aufgerufen"); // Test 1
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

    #[On('confirm-delete')]
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

    #[On('post-created')]
    public function refreshPosts(): void
    {
        unset($this->posts);
    }

}; ?>

<div class="space-y-6"> {{-- Fügt vertikalen Abstand zwischen den Posts hinzu --}}

    {{-- Prüfe, ob Posts vorhanden sind, und loope durch sie hindurch --}}
    @forelse ($this->posts() as $post)
        {{-- Container für jeden einzelnen Post --}}
                <livewire:components.post-item :post="$post" wire:key="post-item-{{ $post->id }}" />
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