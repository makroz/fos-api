<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
  use HasFactory;
  public $with = ['challenge'];
  protected $fillable = [
    'to_date',
    'executed_date',
    'meet_link',
    'points',
    'status',
    'member_id',
    'challenge_id',
    'level_id',
  ];

  //relacion con la tabla challenges
  function challenge()
  {
    return $this->belongsTo(Challenge::class);
  }
}