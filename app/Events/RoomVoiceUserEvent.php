<?php

namespace App\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class RoomVoiceUserEvent implements ShouldBroadcast
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  public $voice_user_id;
  public $room_id;

  public function __construct($room_id, $voice_user_id)
  {
    $this->room_id = $room_id;
    $this->voice_user_id = $voice_user_id;
  }

  public function broadcastOn()
  {
    return ["room{$this->room_id}-channel"];
  }

  public function broadcastAs()
  {
    return "room-voice-{$this->room_id}-event";
  }
}
