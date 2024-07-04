<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Tache;

class Historique extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'user_id',
        'tache_id',
        'state',
        'user_confirm_id'
    ];

    //One to Many et son inverse est hasMany
    public function user() {
        return $this->belongsTo(User::class);
    }

    //Many to Many
    public function taches() {
        return $this->belongsToMany(Tache::class)->select('taches.id', 'taches.name');
    }
}
