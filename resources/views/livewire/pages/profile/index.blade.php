<?php

declare(strict_types=1);

namespace App\Livewire\Pages\Profile;

use App\Data\ProfileData;
use App\Models\User;
use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Title};


new
#[Layout('layouts.app')]
#[Title('Profile')] 
class extends Component 
{ 
    public ProfileData $profile;

    public function mount(string $username): void
    {
        $user = User::with('profile')->where('username', $username)->firstOrFail();
        $this->profile = ProfileData::fromModel($user);
    }
}
?>

<div>
    <div class="relative w-full h-64 mb-16">
        {{-- Headerbild (Testbild) --}}
        <img src="https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=1200&q=80"
            alt="Header"
            class="object-cover w-full h-full rounded-b-xl shadow" />

            {{-- Button-Gruppe oben rechts im Headerbild --}}
        <div class="absolute top-4 right-4 flex space-x-2 z-10">
            @if(auth()->check() && auth()->id() !== $profile->userId)
                <livewire:components.follow-button :user-id="$profile->userId" />
            @endif
            {{-- Platz für weitere Buttons --}}
            {{-- <button class="px-4 py-2 rounded bg-gray-200">...</button> --}}
        </div>
        {{-- Nutzerbild, unten links, rund, leicht überlappend --}}
        <div class="absolute -bottom-12 left-8">
            <img src="{{ $profile->avatar }}"
                alt="Profilbild"
                class="w-24 h-24 rounded-full border-4 border-white shadow-lg object-cover bg-gray-200" />
        </div>

    {{-- Statistiken: Posts, Follower, Following --}}
        <div class="absolute bottom-4 left-40 flex space-x-8 bg-white/80 rounded-lg px-6 py-2 shadow z-10">
            <div class="flex flex-col items-center">
                <span class="font-bold text-lg">{{ $profile->postsCount }}</span>
                <span class="text-xs text-gray-500">Beiträge</span>
            </div>
            <button 
                type="button"
                class="flex flex-col items-center focus:outline-none hover:text-blue-600 transition"
                wire:click="$dispatch('open-modal', { component: 'components.follow-list', data: @js(['userId' => $profile->userId, 'type' => 'followers']) })"
            >
                <span class="font-bold text-lg">{{ $profile->followersCount }}</span>
                <span class="text-xs text-gray-500">Follower</span>
            </button>
            <button
                type="button"
                class="flex flex-col items-center focus:outline-none hover:text-blue-600 transition"
                wire:click="$dispatch('open-modal', { component: 'components.follow-list', data: @js(['userId' => $profile->userId, 'type' => 'following']) })"
            >
                <span class="font-bold text-lg">{{ $profile->followingCount }}</span>
                <span class="text-xs text-gray-500">Folgt</span>
            </button>
        </div>
    </div>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4"> 
            <div>
                @auth
                    @if(auth()->id() === $profile->userId)
                        <div class="p-6">
                            <livewire:components.createpost />
                        </div>
                    @endif
                @endauth
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <livewire:components.timeline :user-id="$profile->userId" />
                </div>
            </div>
        </div>
    </div>
</div>