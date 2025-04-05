<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use HasFactory;

    // Wenn du nicht alle Felder massenweise füllen willst, gib nur die erlaubten Felder an
    protected $fillable = [
        'user_id',
        'content',
    ];

    /**
     * Ein Post gehört zu einem User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
