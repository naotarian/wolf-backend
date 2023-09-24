<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class MyEvent implements ShouldBroadcast
{
  use Dispatchable, InteractsWithSockets, SerializesModels;

  public $message;
  // public $test;

  public function __construct($message)
  {
    \Log::info($message);
    $this->message = $message;
    // $this->test = 'rrr';
  }

  public function broadcastOn()
  {
    // return new Channel('my-channel', $this->message);
    return ['my-channel'];
  }

  public function broadcastAs()
  {
    return 'my-event';
  }
}
