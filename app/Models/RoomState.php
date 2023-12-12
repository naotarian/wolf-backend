<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoomState extends Model
{
    use HasFactory;
    use softDeletes;
    protected $fillable = [
        'room_id',
        'day_number',
        'turn',
    ];
}
