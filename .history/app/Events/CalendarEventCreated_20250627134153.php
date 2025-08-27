<?php

namespace App\Events;

use App\Models\CalendarEvent;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CalendarEventCreated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $event;

    public function __construct(CalendarEvent $event)
    {
        $this->event = $event;
    }

    public function broadcastOn()
    {
        return new Channel('calendar-events');
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->event->id,
            'title' => $this->event->title,
            'start' => $this->event->start,
            'end' => $this->event->end,
            'description' => $this->event->description,
            'user_id' => $this->event->user_id,
        ];
    }
}