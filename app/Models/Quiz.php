<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
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
        'id','label', 'visible','isExam','module' , 'order'
    ];

    protected static function boot() {
        parent::boot();
        static::addGlobalScope('order', function (Builder $builder) {
            $builder->orderBy('order');
        });
    }

    public function tests()
    {
        return $this->belongsToMany(Test::class);
    }

    public static function makeId(string $tile){
        return Str::snake($tile);
    }
}
