<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class RoomSituationEvent implements ShouldBroadcast
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  public $room_id;
  public $situation;

  public function __construct($room_id, $situation)
  {
    $this->room_id = $room_id;
    $this->situation = $situation;
  }

  public function broadcastOn()
  {
    return ["room{$this->room_id}-channel"];
  }

  public function broadcastAs()
  {
    return "room-situation-{$this->room_id}-event";
  }
}
