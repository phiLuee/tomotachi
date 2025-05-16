<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\User;
use Livewire\Wireable;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Concerns\WireableData;

class ProfileData extends Data implements Wireable
{
    use WireableData;

    public function __construct(
        public int $userId,
        public string $username,
        public string $name,
        public ?string $avatar,
        public ?string $bio,
        public ?string $location,
        public ?string $website,
        public int $followersCount,
        public int $followingCount,
        public int $postsCount,
        public string $created_at,
        public string $updated_at,
        public ?string $last_activity,
    ) {}

    /**
     * @param User $user
     * @return static
     */
    public static function fromModel(User $user): self
    {
        $profile = $user->profile;

        return new self(
            $user->id,
            $user->username,
            $user->name,
            $profile?->avatar,
            $profile?->bio,
            $profile?->location,
            $profile?->website,
            $user->followers()->count(),
            $user->following()->count(),
            $user->posts()->count(),
            $user->created_at?->format('d.m.Y'),
            $user->updated_at?->format('d.m.Y'),
            $user->last_activity?->format('d.m.Y H:i'),
        );
    }
}
