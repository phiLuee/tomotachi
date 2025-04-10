<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    use HasFactory;

    protected $table = 'media';

    // Spalten, die füllbar sind (Mass Assignment)
    protected $fillable = [
        'mediable_id',
        'mediable_type',
        'disk',
        'file_path',
        'mime_type',
        'file_size',
    ];

    /**
     * Polymorphe Beziehung: Dieses Media gehört zu einem Model (z. B. Post, Profile, etc.)
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function mediable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Beziehung zu dem User, der die Datei hochgeladen hat.
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
 
    /** 
     * Hilfsfunktion, um eine vollständige URL oder Pfad zurückzugeben
     * @return string
     */
    public function getUrlAttribute(): string
    {
        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk($this->disk);
        return $disk->url($this->file_path);
    }
}
