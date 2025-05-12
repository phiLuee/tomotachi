<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class ChatMessage extends Model
{
    protected $fillable = ['chat_thread_id', 'user_id', 'content'];

    /**
     * Get the chat thread associated with the message.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function thread(): BelongsTo
    {
        return $this->belongsTo(\App\Models\ChatThread::class, 'chat_thread_id');
    }

    /**
     * Get the user who sent the message.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the media associated with the message.
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable');
    }
}
