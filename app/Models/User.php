<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    use HasRoles, HasFactory, Notifiable;

    /**
     * Determine if the user can access the Filament admin panel.
     *
     * @return bool
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasRole(['admin']) && $this->hasVerifiedEmail();
    }


    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * The attributes that should be appended to the model's array form.
     *
     * @var list<string>
     */
    protected static function booted()
    {
        static::created(function ($user) {
            $user->profile()->create();
        });
    }

    /**
     * Beziehung: Ein User hat genau ein Profile (1:1)
     */
    public function profile()
    {
        return $this->hasOne(\App\Models\Profile::class);
    }

    /**
     * Ein User hat viele Posts
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

    /**
     * Die User, denen dieser User folgt (Following).
     */
    public function following(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,        // Verknüpftes Model
            'follows',          // Name der Pivot-Tabelle
            'follower_id',      // Foreign Key des aktuellen Models in der Pivot-Tabelle
            'following_id'      // Foreign Key des verknüpften Models in der Pivot-Tabelle
        )->withTimestamps();     // Optional: Timestamps der Pivot-Tabelle laden
    }

    /**
     * Die User, die diesem User folgen (Followers).
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,        // Verknüpftes Model
            'follows',          // Name der Pivot-Tabelle
            'following_id',     // Foreign Key des *anderen* Models in der Pivot-Tabelle
            'follower_id'       // Foreign Key des *aktuellen* Models in der Pivot-Tabelle
        )->withTimestamps();     // Optional: Timestamps der Pivot-Tabelle laden
    }

    /**
     * Get the chat threads where the user is a participant
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function chatThreads(): BelongsToMany
    {
        return $this->belongsToMany(
            ChatThread::class,
            'chat_thread_user'
        )->withTimestamps()->withPivot('role');
    }

    /**
     * Get all chat messages sent by the user
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function chatMessages(): HasMany
    {
        return $this->hasMany(ChatMessage::class);
    }

    /**
     * Die Posts, die dieser Benutzer geliked hat.
     */
    public function likedPosts(): BelongsToMany
    {
        return $this->belongsToMany(
            Post::class,        // Verknüpftes Model
            'likes',            // Name der Pivot-Tabelle
            'user_id',          // Foreign Key des aktuellen Models (User) in der Pivot-Tabelle
            'post_id'           // Foreign Key des verknüpften Models (Post) in der Pivot-Tabelle
        )->withTimestamps();     // Lädt die created_at/updated_at Timestamps der Pivot-Tabelle
    }
}
