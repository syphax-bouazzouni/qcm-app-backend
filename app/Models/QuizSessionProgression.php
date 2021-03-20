<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuizSessionProgression extends Model
{
    use HasFactory;
    protected $fillable = [
          'success', 'error' , 'notRespond' ,'rest' ,'quiz'
    ];
    protected $casts = [
        'success' => 'float', 'error' => 'float', 'notRespond' => 'integer','rest' => 'integer'
    ];
}
