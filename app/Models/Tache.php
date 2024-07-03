<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Foyer;

class Tache extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'color',
        'foyer_id',
    ];

    public function foyer() {
        return $this->belongsTo(Foyer::class);
    }

    //Many to Many
    public function historiques() {
        return $this->belongsToMany(Historique::class);
    }
}
