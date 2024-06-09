<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Foyer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'admin_id',
        'image',
    ];

    public function user() {
        return $this->hasMany(User::class);
    }
}
