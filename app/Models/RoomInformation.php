<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class RoomInformation extends Model
{
    use HasFactory;
    use HasUuids;
    use softDeletes;
    protected $fillable = [
        'room_id',
        'rule',
    ];
}
