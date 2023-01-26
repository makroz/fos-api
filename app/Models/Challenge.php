<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Challenge extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'time_begin',
        'duration',
        'repeat',
        'separation',
        'position',
        'points',
        'level_id',
        'status',
        'type',
        'meet_link',
    ];
}
