<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Room extends Model
{
    use HasFactory;
    use HasUuids;
    use softDeletes;
    protected $fillable = [
        'master_user_id',
    ];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }
    public function room_master()
    {
        return $this->hasOne(User::class, 'id', 'master_user_id');
    }
}
