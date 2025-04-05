<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

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
     * Ein User hat viele Posts
     */
    public function posts()
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
}
