<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ability extends Model
{
    use HasFactory;
    //indicar que no usa id numerico ni autoincrementable, que solo es string de 5 caracteres

    protected $fillable = [
        'name',
        'description',
    ];
}
