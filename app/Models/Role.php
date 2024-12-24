<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'house_id',
        'manage_priv_priv',
        'manage_house_priv',
        'manage_member_priv',
        'manage_task_priv',
        'manage_priv_priv',
    ];

    public function house(): BelongsTo {
        return $this->belongsTo(House::class);
    }

    public function user(): HasMany {
        return $this->hasMany(User::class);
    }
}
