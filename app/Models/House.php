<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class House extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'logo',
    ];

    public function event(): HasMany {
        return $this->hasMany(Event::class);
    }

    public function role(): HasMany {
        return $this->hasMany(Role::class);
    }

    public function task(): HasMany {
        return $this->hasMany(Task::class);
    }

    public function user(): HasMany {
        return $this->hasMany(User::class);
    }
}
