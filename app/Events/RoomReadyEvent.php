<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class RoomReadyEvent implements ShouldBroadcast
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  public $room_id;
  public $phase;

  public function __construct($room_id, $phase)
  {
    $this->room_id = $room_id;
    $this->phase = $phase;
  }

  public function broadcastOn()
  {
    return ["room{$this->room_id}-channel"];
  }

  public function broadcastAs()
  {
    return "room-phase-{$this->room_id}-event";
  }
}
