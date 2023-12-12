<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomUser extends Model
{
  use HasFactory;
  protected $table = 'room_user';
  protected $fillable = [
    'room_id',
    'user_id',
  ];

  public function cast()
  {
    return $this->hasOne(Cast::class, 'id', 'room_user_id');
  }
}
