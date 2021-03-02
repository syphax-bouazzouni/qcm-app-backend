<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    use HasFactory;

    protected $fillable = [
        'text','type', 'source','quiz'
    ];

    public function questions()
    {
        return $this->belongsToMany(Question::class);
    }

    public function quizzes()
    {
        return $this->belongsToMany(Quiz::class);
    }
}
