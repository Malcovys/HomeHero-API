<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'house_id',
        'frequency',
        'required_member',
    ];

    public function house(): BelongsTo {
        return $this->belongsTo(House::class);
    }

    public function userTasks(): HasMany {
        return $this->hasMany(UserTask::class);
    }
}
