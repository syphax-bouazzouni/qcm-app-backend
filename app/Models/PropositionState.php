<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropositionState extends Model
{
    use HasFactory;
    protected $fillable = [
        'question' ,'propositionsState' ,'proposition' ,'isResponse'
    ];

    public function question()
    {
        return $this->belongsTo(Question::class , 'question');
    }
}
