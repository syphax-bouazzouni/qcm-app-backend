<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;
    protected $fillable = [
        'text','type','quiz' , 'test'
    ];

    public function propositions(){
        return $this->hasMany(Proposition::class , 'question');
    }
}
