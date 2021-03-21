<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionSession extends Model
{
    use HasFactory;
    protected $fillable = [
        'test' , 'state' , 'type' , 'text' , 'explication' , 'note' , 'isQrocResponded' ,'question'
    ];

    public function test()
    {
        return $this->belongsTo(TestSession::class , 'test');
    }

    public function propositionsState()
    {
        return $this->hasMany(PropositionState::class , 'question')->orderBy('id');
    }

}
