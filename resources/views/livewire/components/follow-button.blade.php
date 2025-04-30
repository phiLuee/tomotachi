<?php

declare(strict_types=1);

namespace App\Livewire\Components;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Volt\Component;
use Illuminate\Contracts\Auth\Authenticatable; // Import Authenticatable

new class extends Component
{
    /**
     * The user profile being viewed (potential target for following).
     */
    public User $user;

    /**
     * Indicates if the currently authenticated user is viewing their own profile.
     */
    public bool $isSelf = false;

    /**
     * Mount the component and initialize state.
     *
     * @param \App\Models\User $user The user associated with this follow button.
     */
    public function mount(User $user): void
    {
        $this->user = $user;
        $this->isSelf = Auth::id() === $this->user->id;
    }

    /**
     * Check if the authenticated user is currently following the displayed user.
     * Computed property caches the result per request.
     */
    #[Computed]
    public function isFollowing(): bool
    {
        /** @var \App\Models\User|null $authUser */
        $authUser = Auth::user();

        // Cannot follow if not logged in or if it's the own profile
        if (!$authUser || $this->isSelf) {
            return false;
        }

        // Check if a following relationship exists
        // Assumes a 'following' belongsToMany relationship is defined on the User model
        return $authUser->following()->where('users.id', $this->user->id)->exists();
    }

    /**
     * Toggle the following status for the authenticated user.
     */
    public function toggleFollow(): void
    {
        /** @var \App\Models\User|null $authUser */
        $authUser = Auth::user();

        // Double-check authorization: must be logged in and not self
        if (!$authUser || $this->isSelf) {
            // Optionally dispatch an error notification or log this attempt
             // $this->dispatch('notify', 'Action not allowed.', 'error');
            return;
        }

        // Attach or detach the user ID in the pivot table
        $authUser->following()->toggle($this->user->id);

        // Dispatch an event that other components might listen to (e.g., update follower count)
        $this->dispatch('follow-toggled', userId: $this->user->id);
    }
}; 
?>


<div>
    {{-- Show button only if logged in and not viewing own profile --}}
    @if(auth()->check() && !$this->isSelf)
        <button
            wire:click="toggleFollow"
            wire:loading.attr="disabled" {{-- Disable button while action is pending --}}
            wire:loading.class="opacity-75" {{-- Optionally visually indicate loading --}}
            class="px-4 py-2 rounded-md font-semibold text-xs transition duration-150 ease-in-out
                   {{ $this->isFollowing ?
                       'bg-gray-200 text-gray-700 hover:bg-gray-300 dark:bg-gray-600 dark:text-gray-200 dark:hover:bg-gray-500' :
                       'bg-blue-600 text-white hover:bg-blue-700 dark:hover:bg-blue-500'
                   }}"
        >
            {{-- Loading indicator inside the button (optional) --}}
            <span wire:loading wire:target="toggleFollow" class="inline-block mr-1">
                <svg class="animate-spin h-4 w-4 text-current" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </span>

            {{-- Button Text --}}
            <span wire:loading.remove wire:target="toggleFollow">
                 {{ $this->isFollowing ? __('Unfollow') : __('Follow') }}
            </span>
             {{-- Alternate text while loading --}}
             <span wire:loading wire:target="toggleFollow">
                 {{ $this->isFollowing ? __('Unfollowing...') : __('Following...') }}
             </span>
        </button>
    @endif
</div>