<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'house_id',
        'name',
        'description',
        'date',
    ];

    public function house(): BelongsTo {
        return $this->belongsTo(House::class);
    }
}
