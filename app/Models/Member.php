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
}
