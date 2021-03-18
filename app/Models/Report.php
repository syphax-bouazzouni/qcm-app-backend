<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;
    protected $fillable = [
        'subject', 'text' , 'user' , 'test'
    ];

    public function test()
    {
        return $this->belongsTo(Test::class , 'test');
    }

    public function user()
    {
        return $this->belongsTo(User::class , 'user');
    }
}