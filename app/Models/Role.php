<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'house_id',
        'namage_priv_priv',
        'manage_house_priv',
        'manage_member_priv',
        'manage_task_priv',
        'manage_even_priv',
        'manage_priv_priv',
    ];
}
