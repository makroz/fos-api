<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Live extends Model
{
    use HasFactory;
    public $with = ['user'];
    protected $fillable = [
        'open_date',
        'close_date',
        'meet_link',
        'cant',
        'cant_asist',
        'cant_aproved',
        'cant_cancel',
        'status',
        'user_id',
        'challenge_id',
    ];

    //relacion con la tabla challenges
    function challenge()
    {
        return $this->belongsTo(Challenge::class);
    }

    //relacion con la tabla users
    function user()
    {
        return $this->belongsTo(User::class);
    }

    //relacion con la tabla tasks
    function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
