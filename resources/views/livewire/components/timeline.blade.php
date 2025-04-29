<?php
declare(strict_types=1);

use App\Models\Post; 
use Illuminate\Support\Facades\Auth; 
use Illuminate\Database\Eloquent\Collection; 
use Livewire\Volt\Component;
use Livewire\Attributes\Computed; 
use Livewire\Attributes\On;
use Livewire\WithPagination;
use Illuminate\Pagination\LengthAwarePaginator;

new class extends Component
{
    use WithPagination;

    public ?int $userId = null;
    public Collection $accumulatedPosts;

    /**
     * Berechne die Posts für die Timeline.
     * #[Computed] sorgt dafür, dass das Ergebnis für die Dauer
     * eines Requests gecached wird, um unnötige Datenbankabfragen
     * bei mehreren Zugriffen im selben Render-Zyklus zu vermeiden.
     */
    #[Computed]
    public function posts(): LengthAwarePaginator
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
                    ->addSelect([
                     'is_liked_by_current_user' => Post::selectRaw('COUNT(*) > 0')
                         ->from('likes')
                         ->whereColumn('likes.post_id', 'posts.id')
                         ->where('likes.user_id', Auth::id())
                    ])
                    ->latest() // Sortiere nach Datum
                    ->paginate(10); // Hole die Ergebnisse
    }

    /**
     * Initialisiert die Komponente mit der übergebenen userId.
     * Wenn keine userId übergeben wird, werden die Posts
     * von den Usern angezeigt, denen der eingeloggte User folgt.
     */
    public function mount(?int $userId = null): void
    {
        $this->userId = $userId;
        $this->gotoPage(1);
        $this->accumulatedPosts = new Collection($this->posts()->items());
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
        $this->refreshPosts();

        $this->dispatch('notify', 'Post erfolgreich gelöscht!');
    }

    #[On('post-created')]
    public function refreshPosts(): void
    {
        // Paginierung zurücksetzen und Computed Property Cache leeren
        $this->resetPage();
        unset($this->posts);

        // Akkumulierte Posts neu mit Seite 1 laden
        $this->accumulatedPosts = new Collection($this->posts()->items());
    }

    /**
     * Diese Methode wird aufgerufen, wenn der Benutzer auf "Mehr laden" klickt.
     * Sie lädt die nächste Seite der Pagination.
     */
    public function loadMore(): void
    {
        $this->nextPage();
        // Hole die Posts der *neuen* aktuellen Seite
        $newPosts = $this->posts()->items();
        // Füge die neuen Posts zu den bereits vorhandenen hinzu
        $this->accumulatedPosts = $this->accumulatedPosts->concat($newPosts);
    }

}; ?>

<div class="space-y-6"> {{-- Fügt vertikalen Abstand zwischen den Posts hinzu --}}
    {{-- Prüfe, ob Posts vorhanden sind, und loope durch sie hindurch --}}
    @forelse ($accumulatedPosts as $post)
        {{-- Container für jeden einzelnen Post --}}
        <livewire:components.post-item :post="$post" wire:key="post-item-{{ $post->id }}" />
    @empty
        {{-- Nachricht, wenn keine Posts vorhanden sind --}}
        <div class="text-center text-gray-500 dark:text-gray-400 py-8">
            <p>Noch keine Posts vorhanden.</p>
            <p class="mt-2">Folge anderen Nutzern, um ihre Posts hier zu sehen!</p>
        </div>
    @endforelse

    {{-- Infinite Scrolling --}}
    @if ($this->posts()->hasMorePages())
        <div
            x-data="{
                observe() {
                    let observer = new IntersectionObserver((entries) => {
                        entries.forEach(entry => {
                            if (entry.isIntersecting) {
                                @this.call('loadMore');
                            }
                        });
                    });

                    observer.observe(this.$el);
                }
            }"
            x-init="observe"
            class="text-center py-4 text-gray-500"
        >
            Lädt weitere Posts...
        </div>
    @endif
</div>