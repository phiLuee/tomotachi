<?php

declare(strict_types=1);

use App\Models\User;
use Livewire\Volt\Component;

new class extends Component
{
    public int $userId;
    public string $type = 'followers';

    public User $user;
    public \Illuminate\Support\Collection $users;

    public function mount(int $userId, string $type = 'followers'): void
    {
        $this->userId = $userId;
        $this->type = $type;
        $this->user = User::findOrFail($userId);

        $this->users = $type === 'followers'
            ? $this->user->followers()->get()
            : $this->user->following()->get();
    }
}; ?>

<div>
    <h2 class="text-lg font-bold mb-4 text-center">
        {{ $type === 'followers' ? 'Follower' : 'Folgt' }}
    </h2>
    <ul>
        @forelse($users as $user)
            <li class="py-2 border-b last:border-b-0">
                <a href="{{ route('profile.show', $user->username) }}" class="flex items-center space-x-3 hover:bg-gray-50 rounded px-2 py-1 transition">
                    <img src="{{ $user->profile_image ?? 'https://ui-avatars.com/api/?name=' . urlencode($user->username) }}"
                         class="w-8 h-8 rounded-full object-cover bg-gray-200" alt="{{ $user->username }}" />
                    <span class="font-medium">{{ $user->username }}</span>
                </a>
            </li>
        @empty
            <li class="py-4 text-center text-gray-400">
                {{ $type === 'followers' ? 'Noch keine Follower.' : 'Folgt noch niemandem.' }}
            </li>
        @endforelse
    </ul>
</div>