<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\Post;
use Carbon\Carbon;
use Livewire\Wireable;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Concerns\WireableData;

class PostData extends Data implements Wireable
{
    use WireableData;

    public function __construct(
        public int $id,
        public string $content,
        public Carbon $created_at,
        public Carbon $updated_at,
        public int $likers_count,
        public bool $is_liked_by_current_user,
        public UserData $user,
    ) {}

    public static function fromModel(Post $post): self
    {
        return new self(
            $post->id,
            $post->content,
            $post->created_at,
            $post->updated_at,
            $post->likers_count,
            (bool)$post->is_liked_by_current_user,
            UserData::from($post->user),
        );
    }
}
