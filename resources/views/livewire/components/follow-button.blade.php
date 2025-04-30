<?php

declare(strict_types=1);

namespace App\Livewire\Components;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Volt\Component;

new class extends Component
{
    public User $user;

    public function getIsFollowingProperty(): bool
    {
        $authUser = Auth::user();
        if (!$authUser || $authUser->id === $this->user->id) {
            return false;
        }
        return $authUser->following()->where('users.id', $this->user->id)->exists();
    }

    public function toggleFollow()
    {
        $authUser = Auth::user();
        if (!$authUser || $authUser->id === $this->user->id) {
            return;
        }
        $authUser->following()->toggle($this->user->id);
        $this->dispatch('follow-toggled');
    }
}

?>


@php
    $isSelf = auth()->id() === $user->id;
@endphp
<div>
    @if(auth()->check() && !$isSelf)
        <button wire:click="toggleFollow"
            class="px-4 py-2 rounded-md font-semibold text-xs
                {{ $this->isFollowing ? 'bg-gray-300 text-gray-700' : 'bg-blue-600 text-white' }}">
            {{ $this->isFollowing ? __('Unfollow') : __('Follow') }}
        </button>
    @endif
</div>