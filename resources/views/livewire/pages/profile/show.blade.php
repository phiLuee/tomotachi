<?php

use App\Models\User;
use Livewire\Volt\Component;
use Livewire\Attributes\{Layout, Title};


new
    #[Layout('layouts.guest')]
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
    <p>Profile: {{ $username }}</p>
    <p>Angemeldet als: {{ auth()->user()?->username }}</p>
</div>