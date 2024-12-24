<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class House extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'emblem_url',
        'config_id',
    ];

    public function roles(): HasMany {
        return $this->hasMany(Role::class);
    }

    public function tasks(): HasMany {
        return $this->hasMany(Task::class);
    }

    public function users(): HasMany {
        return $this->hasMany(User::class);
    }

    public function config(): BelongsTo {
        return $this->BelongsTo(HouseConfig::class);
    }
}
