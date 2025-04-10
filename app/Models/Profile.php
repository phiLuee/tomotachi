<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Profile extends Model
{
    use HasFactory;

    // Falls du explizit machen willst, wie die Tabelle heißt:
    // protected $table = 'profiles';

    /**
     * Mass-assignable Attribute
     */
    protected $fillable = [
        'user_id',
        'avatar',
        'bio',
        'location',
        'website',
    ];

    /**
     * Relationship: Ein Profile gehört genau zu einem User (1:1)
     * @return \Illuminate\Database\Eloquent\Factories\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Beispiel-Accessor: Gebe einen "vollständigen" Avatar-Pfad zurück,
     * falls du mit Storage-Disks arbeitest.
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function getAvatarUrlAttribute(): MorphOne
    {
        return $this->morphOne(Media::class, 'mediable')->where('role', 'avatar');
    }
}
