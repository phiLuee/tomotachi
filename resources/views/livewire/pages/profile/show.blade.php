<?php

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