<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CalendarEventDeleted implements ShouldBroadcastNow
{
    use Dispatchable, SerializesModels;

    public $eventId;

    public function __construct($eventId)
    {
        $this->eventId = $eventId;
    }

    public function broadcastOn()
    {
        return new Channel('calendar-events');
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->eventId,
        ];
    }
}