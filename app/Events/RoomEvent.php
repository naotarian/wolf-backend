<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class RoomEvent implements ShouldBroadcast
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  public $room_id;
  public $users;

  public function __construct($room_id, $users)
  {
    // \Log::info($users);
    // \Log::info($room_id);
    $this->room_id = $room_id;
    $this->users = $users;
  }

  public function broadcastOn()
  {
    return ["room{$this->room_id}-channel"];
  }

  public function broadcastAs()
  {
    return "room{$this->room_id}-event";
  }
}
