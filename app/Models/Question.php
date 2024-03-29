<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;
    protected $fillable = [
        'text','type','quiz' ,'test' ,'explication'
    ];

    public function propositions(){
        return $this->hasMany(Proposition::class , 'question');
    }
    public function tests()
    {
        return $this->belongsToMany(Test::class);
    }

    public function sessions(){
        return $this->hasMany(QuestionSession::class , 'question');
    }
}
