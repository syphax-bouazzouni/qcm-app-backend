<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestSession extends Model
{
    use HasFactory;
    protected $fillable = [
        'quiz' , 'timer' , 'text' , 'state' , 'source' , 'type' , 'quizLabel' ,'test'
    ];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class , 'quiz');
    }

    public function questions()
    {
        return $this->hasMany(QuestionSession::class , 'test')->orderBy('id');
    }
}
