<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;
    protected $primaryKey = 'title';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['title'];
}
