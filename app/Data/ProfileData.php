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
        public int $id,
        public string $username,
        public ?string $name,
        public ?string $profile_image,
        public ?string $bio,
        public int $followers_count,
        public int $following_count,
        public int $posts_count,
        public string $created_at,
        public string $updated_at,
        public ?string $last_activity,
    ) {}

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
