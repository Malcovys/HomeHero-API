<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Foyer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'user_id',
        'admin_id',
        'image',
    ];
}
