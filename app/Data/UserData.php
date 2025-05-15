<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\User;
use Livewire\Wireable;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Concerns\WireableData;

class UserData extends Data implements Wireable
{
    use WireableData;

    public function __construct(
        public int $id,
        public string $username,
        public ?string $name,
        public ?string $avatar,
    ) {}

    public static function fromModel(User $user): self
    {
        return new self(
            $user->id,
            $user->username,
            $user->name,
            $user->profile?->avatar, // Bild aus Profile-Relation
        );
    }
}
