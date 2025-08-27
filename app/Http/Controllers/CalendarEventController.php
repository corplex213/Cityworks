<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CalendarEvent;
use App\Events\CalendarEventCreated;
use App\Events\CalendarEventDeleted;
use Spatie\Activitylog\Models\Activity;
use App\Services\NotificationService;

class CalendarEventController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'start' => 'required|date',
            'end' => 'nullable|date|after_or_equal:start',
            'description' => 'nullable|string',
        ]);

        $event = CalendarEvent::create([
            'user_id' => $request->user()->id,
            'title' => $request->title,
            'start' => $request->start,
            'end' => $request->end,
            'description' => $request->description,
        ]);
        // Log activity
        activity()
            ->causedBy($request->user())
            ->performedOn($event)
            ->log('Created calendar event: ' . $event->title);

        event(new CalendarEventCreated($event));
        
        $notification = $this->notificationService->createForUser(
            $request->user()->id,
            'calendar_event',
            'New Calendar Event Added',
            'Event "' . $event->title . '" has been added to your calendar.',
            route('calendar')
        );

        // Broadcast the notification for real-time updates
        event(new \App\Events\NotificationCreated($notification));

        return response()->json([
            'id' => $event->id,
            'title' => $event->title,
            'start' => $event->start,
            'end' => $event->end,
            'className' => 'calendar-event',
            'extendedProps' => [
                'description' => $event->description,
                'assigned_to' => $request->user()->name,
                'status' => null,
                'project' => null,
                'priority' => null,
            ]
        ]);
    }

    public function events(Request $request)
    {
        $user = $request->user();
        $events = CalendarEvent::where('user_id', $user->id)->get();

        return $events->map(function ($event) use ($user) {
            return [
                'id' => 'event-' . $event->id, 
                'title' => $event->title,
                'start' => $event->start,
                'end' => $event->end,
                'className' => 'calendar-event',
                'extendedProps' => [
                    'description' => $event->description,
                    'assigned_to' => $user->name,
                    'status' => null,
                    'project' => null,
                    'priority' => null,
                ]
            ];
        });
    }
    public function destroy($id)
    {
        $event = CalendarEvent::findOrFail($id);
        // Log activity
        activity()
            ->causedBy(auth()->user())
            ->performedOn($event)
            ->log('Deleted calendar event: ' . $event->title);
        $event->delete();

        // Broadcast the deletion event
        event(new CalendarEventDeleted($id));

        return response()->json(['success' => true]);
    }
}