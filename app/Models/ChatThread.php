<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ChatThread extends Model
{
    protected $fillable = ['type','name','avatar','is_archived'];

    /**
     * Get the users for the chat thread.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users():BelongsToMany
    {
        return $this->belongsToMany(
            User::class,
            'chat_thread_user'
        )->withTimestamps()->withPivot('role');
    }

    /**
     * Get the messages for the chat thread.
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }

    /**
     * Get the latest message for the chat thread.
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function latestMessage(): HasOne
    {
        return $this->hasOne(ChatMessage::class)->latest();
    }
}
