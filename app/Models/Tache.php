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
        'foyer_id',
    ];

    public function foyer() {
        return $this->belongsTo(Foyer::class);
    }
}
