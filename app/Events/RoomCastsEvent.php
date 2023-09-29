<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class RoomCastsEvent implements ShouldBroadcast
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  public $room_id;
  public $casts;

  public function __construct($room_id, $casts)
  {
    $this->room_id = $room_id;
    $this->casts = $casts;
  }

  public function broadcastOn()
  {
    return ["room{$this->room_id}-channel"];
  }

  public function broadcastAs()
  {
    return "room-casts-{$this->room_id}-event";
  }
}
