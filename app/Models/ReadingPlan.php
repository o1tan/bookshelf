<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReadingPlan extends Model
{
    use HasFactory;

    public const STATUS_NOT_STARTED = 'not_started';

    public const STATUS_READING = 'reading';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_EXPIRED = 'expired';

    protected $fillable = [
        'book_id',
        'deadline',
        'status',
        'reminder_at',
        'reminded_at',
        'completed_at',
    ];

    protected $casts = [
        'deadline' => 'date',
        'reminder_at' => 'datetime',
        'reminded_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }
}
