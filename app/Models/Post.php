<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Auth;

class Post extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'parent_id',
        'content',
    ];

    /**
     * get user who created this post
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * get root post if exist
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'parent_id');
    }

    /**
     * all comments for this post
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function children(): HasMany
    {
        return $this->hasMany(Post::class, 'parent_id');
    }

    /**
     * all root posts
     * @param $query
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * all comments
     * @param $query
     */
    public function scopeComments($query)
    {
        return $query->whereNotNull('parent_id');
    }

    /**
     * media for this post
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    /**
     * Die Benutzer, die diesen Post geliked haben.
     */
    public function likers(): BelongsToMany
    {
        return $this->belongsToMany(
            User::class,        // Verknüpftes Model
            'likes',            // Name der Pivot-Tabelle
            'post_id',          // Foreign Key des aktuellen Models (Post) in der Pivot-Tabelle
            'user_id'           // Foreign Key des verknüpften Models (User) in der Pivot-Tabelle
        )->withTimestamps();     // Lädt die created_at/updated_at Timestamps der Pivot-Tabelle
    }

    /**
     * Prüft, ob der aktuell eingeloggte Benutzer diesen Post geliked hat.
     *
     * @return bool
     */
    public function isLikedByCurrentUser(): bool
    {
        if (!Auth::check()) {
            return false; // Nicht eingeloggte Benutzer können nichts geliked haben
        }

        // Prüft effizient, ob eine Beziehung für den aktuellen Benutzer existiert
        return $this->likers()->where('user_id', Auth::id())->exists();
    }
}
