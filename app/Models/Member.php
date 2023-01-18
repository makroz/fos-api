<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Member extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasUuids;

    protected $with = ['level', 'referidos'];

    protected $fillable = [
        'name',
        'icn',
        'pin',
        'password',
        'register_date',
        'points',
        'status',
        'level_id',
        'sponsor_id',
    ];

    protected $hidden = [
        'password',
        'pin',
    ];

    public $incrementing = false;

    protected $keyType = 'uuid';

    function level()
    {
        return $this->belongsTo(Level::class);
    }

    //relacion con la tabla tasks
    function tasks()
    {
        return $this->hasMany(Task::class);
    }

    //relacion con la tabla members usando el campo sponsor_id
    function sponsor()
    {
        return $this->belongsTo(Member::class, 'sponsor_id')->select('id', 'icn', 'name', 'level_id');
    }

    //relacion inversa con la tabla members usando el campo sponsor_id
    function referidos()
    {
        return $this->hasMany(Member::class, 'sponsor_id');
    }
}
