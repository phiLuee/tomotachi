<?php
declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Notification extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $table = 'notifications';
    
    protected $fillable = [
        'id',
        'type',
        'notifiable_id',
        'notifiable_type',
        'data',
        'read_at'
    ];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    /**
     * Get the user that owns the notification.
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function notifiable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Prüfe, ob die Benachrichtigung schon gelesen wurde.
     * @return bool
     */
    public function isRead(): bool
    {
        return ! is_null($this->read_at);
    }

    /**
     * Markiere die Benachrichtigung als gelesen.
     * @return void
     */
    public function markAsRead(): void
    {
        $this->update(['read_at' => now()]);
    }

    /**
     * Markiere die Benachrichtigung als ungelesen (z.B. wenn das Sinn macht).
     * @return void
     */
    public function markAsUnread(): void
    {
        $this->update(['read_at' => null]);
    }
}
