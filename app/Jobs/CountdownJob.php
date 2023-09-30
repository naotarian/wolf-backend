<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Events\RoomCountdownEvent;

class CountdownJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $count;
    public $room_id;

    /**
     * Create a new job instance.
     */
    public function __construct($room_id, $count)
    {
        $this->count = $count;
        $this->room_id = $room_id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        while ($this->count > 0) {
            event(new RoomCountdownEvent($this->room_id, $this->count));
            $this->count--;
            sleep(1);
        }
    }
}
