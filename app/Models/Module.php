<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Module extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id','title', 'year','image'
    ];

    public static function makeId(string $tile){
        return Str::snake($tile);
    }
    public function offers(){
        return $this->belongsToMany(Offer::class);
    }
    public function quizzes(){
        return $this->hasMany(Quiz::class , 'module');
    }
}
