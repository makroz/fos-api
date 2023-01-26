<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;
    public $with = ['challenge', 'live'];
    protected $fillable = [
        'to_date',
        'start_date',
        'ended_date',
        'points',
        'status',
        'member_id',
        'challenge_id',
        'level_id',
        'live_id',
        'type',
        'user_id'
    ];

    //relacion con la tabla challenges
    function challenge()
    {
        return $this->belongsTo(Challenge::class);
    }

    //relacion con la tabla members
    function member()
    {
        return $this->belongsTo(Member::class);
    }

    //relacion con la tabla levels
    function level()
    {
        return $this->belongsTo(Level::class);
    }

    //relacion con la tabla lives
    function live()
    {
        return $this->belongsTo(Live::class);
    }
}
