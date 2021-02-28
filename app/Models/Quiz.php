<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Quiz extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'id','label', 'visible','isExam','module'
    ];

    public function tests()
    {
        return $this->belongsToMany(Test::class);
    }

    public static function makeId(string $tile){
        return Str::snake($tile);
    }
}
