<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Fav extends Model
{
    use HasFactory;
    protected $fillable = [
        'label', 'user'
    ];

    public function tests()
    {
        return $this->belongsToMany(Test::class);
    }
}
