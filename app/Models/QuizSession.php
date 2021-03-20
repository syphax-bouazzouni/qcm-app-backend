<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizSession extends Model
{
    use HasFactory;
    protected $fillable = [
        'user', 'currentTest' , 'state' , 'isQuiz'
    ];

    public function tests()
    {
        return $this->hasMany(TestSession::class , 'quiz' )->orderBy('id');
    }

    public function quizzes()
    {
        return $this->hasMany(QuizCopy::class , 'quiz')->orderBy('id');
    }


    public function user()
    {
        return $this->belongsTo(User::class , 'user');
    }
    public function progression()
    {
        return $this->hasOne(QuizSessionProgression::class ,'quiz');
    }
}
