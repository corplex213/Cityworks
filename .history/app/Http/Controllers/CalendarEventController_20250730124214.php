<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CalendarEvent;
use App\Events\CalendarEventCreated;
use App\Events\CalendarEventDeleted;
use Spatie\Activitylog\Models\Activity;

class CalendarEventController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:create tasks')->only(['store']);
        $this->middleware('permission:view tasks')->only(['events']);
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
        $event->delete();

        // Broadcast the deletion event
        event(new CalendarEventDeleted($id));

        return response()->json(['success' => true]);
    }
}