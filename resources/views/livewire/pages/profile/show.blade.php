<?php

declare(strict_types=1);

namespace App\Livewire\Pages\Profile;

use App\Models\User;
use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Title};


new
    #[Layout('layouts.app')]
    #[Title('Profile')]
    class extends Component {
        public string $username;
        public ?User $user;

        // Entkommentieren Sie die mount-Methode
        public function mount(string $username): void
        {
            $this->username = $username;
            $this->user = User::where('username', $username)->firstOrFail();
        }
    }; ?>

<div>
    <div class="relative w-full h-64 mb-16">
    {{-- Headerbild (Testbild) --}}
    <img src="https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=1200&q=80"
         alt="Header"
         class="object-cover w-full h-full rounded-b-xl shadow" />


            {{-- Button-Gruppe oben rechts im Headerbild --}}
        <div class="absolute top-4 right-4 flex space-x-2 z-10">
            @if(auth()->check() && auth()->id() !== $user->id)
                <livewire:components.follow-button :user="$user" />
            @endif
            {{-- Platz für weitere Buttons --}}
            {{-- <button class="px-4 py-2 rounded bg-gray-200">...</button> --}}
        </div>
    {{-- Nutzerbild, unten links, rund, leicht überlappend --}}
    <div class="absolute -bottom-12 left-8">
        <img src="{{ $user->profile_image ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->username) }}"
             alt="Profilbild"
             class="w-24 h-24 rounded-full border-4 border-white shadow-lg object-cover bg-gray-200" />
    </div>
</div>
 <div class="py-6">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4"> 
            <div>
                <p>Profile: {{ $username }}</p>
                <p>Angemeldet als: {{ auth()->user()?->username }}</p>
                <p>Benutzer-ID: {{ auth()->user()?->id }}</p>
                <p>Profil-ID: {{ $user->id }}</p>
                <p>Profil-Bild: {{ $user->profile_image }}</p>
                <p>Bio: {{ $user->bio }}</p>
                <p>Follower: {{ $user->followers_count }}</p>
                <p>Folge ich: {{ $user->following_count }}</p>
                <p>Beigetreten am: {{ $user->created_at?->format('d.m.Y') }}</p>
                <p>Aktualisiert am: {{ $user->updated_at?->format('d.m.Y') }}</p>
                <p>Letzte Aktivität: {{ $user->last_activity?->format('d.m.Y H:i') }}</p>
            </div>
            <div>
                @auth
                    <div class="p-6">
                        <livewire:createpost />
                    </div>
                @endauth
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <livewire:components.timeline :user-id="$user->id" />
                </div>
            </div>
        </div>
    </div>
</div> 
</div>