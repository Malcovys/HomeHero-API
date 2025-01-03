<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserTask extends Model
{
    use HasFactory;

    protected $table = "user_tasks";

    protected $fillable = [
        'task_id',
        'user_id',
        'date',
        'complete',
    ];

    public function task(): BelongsTo {
        return $this->belongsTo(Task::class);
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

}
