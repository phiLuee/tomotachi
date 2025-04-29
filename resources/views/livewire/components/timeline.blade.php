<?php

declare(strict_types=1);

namespace App\Livewire;

use App\Models\Post;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\LengthAwarePaginator as ConcreteLengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Volt\Component;
use Livewire\WithPagination;

new class extends Component
{
    use WithPagination;

    /**
     * Optional user ID to display posts only from this user.
     * Null if the logged-in user's timeline should be displayed.
     */
    public ?int $userId = null;

    /**
     * The collection of posts accumulated across multiple pages.
     * Used for infinite scrolling.
     * @var \Illuminate\Support\Collection<int, \App\Models\Post>
     */
    public Collection $accumulatedPosts;


    /**
     * Initializes the component (Livewire lifecycle hook).
     * Sets the initial user ID and loads the first page of posts.
     *
     * @param int|null $userId The ID of the user whose posts should be displayed, or null for the main timeline.
     */
    public function mount(?int $userId = null): void
    {
        $this->userId = $userId;
        $this->resetPage(); // Ensure we start on page 1
        $this->initializeAccumulatedPosts();
    }

    /**
     * Computes the posts for the current timeline page.
     * The result is cached per request (#[Computed]).
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<\App\Models\Post>
     */
    #[Computed(persist: false)] 
    public function posts(): LengthAwarePaginator
    {
        // Guest users: Do not display posts on the main timeline
        if ($this->userId === null && !Auth::check()) {
            return $this->createEmptyPaginator();
        }

        $query = Post::query()
            ->with([
                'user:id,name,username', // Load only specific user fields
            ])
            ->withCount('likers') // Efficiently count the number of likes
            ->addSelect([
                // Check if the current user has liked the post (subquery)
                'is_liked_by_current_user' => Post::selectRaw('COUNT(*) > 0')
                    ->from('likes')
                    ->whereColumn('likes.post_id', 'posts.id')
                    ->where('likes.user_id', Auth::id())
            ])
            ->latest(); // Latest posts first

        if ($this->userId !== null) {
            // Specific user profile: Only posts from this user
            $query->where('user_id', $this->userId);
        } else {
            // Main timeline: Posts from followed users + own posts
            $user = Auth::user(); // Auth::check() was verified above
            $followedUserIds = $user->following()->pluck('users.id');
            $followedUserIds->push($user->id); // Add own ID

            $query->whereIn('user_id', $followedUserIds);
        }

        return $query->paginate(10);
    }

    /**
     * Loads the next page of posts and adds them to the accumulated posts.
     * Triggered by infinite scrolling.
     */
    public function loadMore(): void
    {
        $paginator = $this->posts(); // Get paginator before calling nextPage

        if (!$paginator->hasMorePages()) {
            return; // No more pages available
        }

        $this->nextPage($paginator->getPageName()); // Increment page number for the *next* query
        unset($this->posts); // Clear computed cache so posts() fetches the new page

        $newPosts = $this->posts()->items(); // Get items from the *new* page

        // Add new posts only if any exist
        if (!empty($newPosts)) {
            // accumulatedPosts is guaranteed to be initialized (either by default or in mount)
            $this->accumulatedPosts = $this->accumulatedPosts->concat($newPosts);
        }
    }

    /**
     * Reacts to the 'post-created' event.
     * Resets pagination and reloads the posts.
     */
    #[On('post-created')]
    public function refreshPosts(): void
    {
        $this->resetPage(); // Back to page 1
        unset($this->posts); // Important: Clear computed cache
        $this->initializeAccumulatedPosts(); // Re-initialize accumulated posts
    }

    /**
     * Reacts to the 'confirm-delete' event from a PostItem.
     * Deletes the post after confirmation and authorization check.
     *
     * @param int $postId The ID of the post to delete.
     */
    #[On('confirm-delete')]
    public function deletePost(int $postId): void
    {
        $post = Post::findOrFail($postId);

        // --- Best practice suggestion: Policies ---
        // Instead of manual check:
        // try {
        //     $this->authorize('delete', $post);
        // } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
        //     $this->dispatch('notify', 'You are not authorized to delete this post.', 'error');
        //     return;
        // }
        // -----------------------------------------

        // Current implementation (manual check):
        if (Auth::id() !== $post->user_id) {
            $this->dispatch('notify', 'You can only delete your own posts.', 'error');
            return;
        }

        $post->delete();

        // Remove post from the accumulated list instead of reloading everything
        $this->accumulatedPosts = $this->accumulatedPosts->reject(fn(Post $p) => $p->id === $postId);

        // Optional: If you always want to reload the list after deletion (like before):
        // $this->refreshPosts();

        $this->dispatch('notify', 'Post deleted successfully!', 'success'); // 'success' type is often helpful
    }


    /**
     * Helper method to initialize or reset the accumulated posts.
     */
    private function initializeAccumulatedPosts(): void
    {
        // Overwrites the accumulatedPosts collection with data from the first page
        try {
            $paginator = $this->posts();
            $this->accumulatedPosts = new Collection($paginator->items());
        } catch (\Exception $e) {
            // Log error or handle appropriately
            logger()->error('Failed to initialize timeline posts: ' . $e->getMessage());
            $this->dispatch('notify', 'Error loading timeline.', 'error');
        }
    }

    /**
     * Creates an empty paginator.
     * Useful when no results are available (e.g., for guests).
     */
    private function createEmptyPaginator(): LengthAwarePaginator
    {
        return new ConcreteLengthAwarePaginator(
            items: [],
            total: 0,
            perPage: 10,
            currentPage: $this->getPage() // Use current page
        );
    }
}; ?>

<div class="space-y-6"> {{-- Vertical space between posts --}}

    {{-- Loop through the accumulated posts --}}
    @forelse ($accumulatedPosts as $post)
        <livewire:components.post-item :post="$post" wire:key="post-item-{{ $post->id }}-{{ $post->updated_at }}" />
    @empty
        {{-- Message displayed when no posts are available --}}
        <div class="text-center text-gray-500 dark:text-gray-400 py-8">
            @if($this->userId === null && !Auth::check())
                <p>Log in to see your timeline.</p>
            @elseif($this->userId !== null)
                <p>This user hasn't published any posts yet.</p>
            @else
                <p>No posts available yet.</p>
                <p class="mt-2">Follow other users or create your first post!</p>
            @endif
        </div>
    @endforelse

    {{-- Infinite Scrolling Trigger --}}
    {{-- Show the "Loading..." element only if there are potentially more pages --}}
    @if ($this->posts()->hasMorePages())
         {{-- Show loading indicator only when loading more --}}
        <div wire:loading.delay wire:target="loadMore" class="text-center py-4 text-gray-500 dark:text-gray-400">
            {{-- Optional: Visual loading indicator --}}
            <svg class="animate-spin h-5 w-5 text-gray-500 mx-auto" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span class="mt-2 block">Loading more posts...</span>
       </div>

        {{-- The actual IntersectionObserver trigger element (can be empty or contain non-loading content) --}}
        <div
             {{-- Don't show the trigger if loading is already in progress --}}
             wire:loading.remove wire:target="loadMore"
             x-data="{
                 observe() {
                     // Initialize IntersectionObserver
                     let observer = new IntersectionObserver((entries) => {
                         entries.forEach(entry => {
                             // If the element becomes visible...
                             if (entry.isIntersecting) {
                                 // ...call the Livewire 'loadMore' method.
                                 @this.call('loadMore');
                             }
                         });
                     }, { threshold: 0.1 }); // threshold (optional) determines how much must be visible

                     // Start observing this element
                     observer.observe(this.$el);
                 }
             }"
             x-init="observe"
             class="h-10" {{-- Needs some height to be intersectable --}}
         >
              {{-- Can be empty or contain a subtle marker --}}
         </div>
    @endif
</div>