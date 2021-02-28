<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Offer extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';

    public $incrementing = false;

    protected $keyType = 'string';
    protected $fillable = [
        'id','title', 'price','state','image'
    ];

    public static function makeId(string $tile){
        return Str::snake($tile);
    }
}
