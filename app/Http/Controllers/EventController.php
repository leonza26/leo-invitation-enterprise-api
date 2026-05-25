<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        if ($user->hasRole('super_admin') || $user->hasRole('operator')) {
            $events = Event::with('client')->latest()->get();
        } else {
            $events = Event::where('client_id', $user->id)->latest()->get();
        }

        return response()->json(['events' => $events]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_name' => 'required|string|max:255',
            'event_type' => 'nullable|string',
            'theme' => 'nullable|string',
            'venue' => 'nullable|string',
            'event_date' => 'nullable|date',
            'description' => 'nullable|string',
            'package' => 'nullable|string|in:basic,premium,luxury',
        ]);

        $event = Event::create(array_merge($validated, [
            'client_id' => $request->user()->id,
        ]));

        return response()->json(['event' => $event], 201);
    }

    public function show(Request $request, Event $event)
    {
        // Simple auth check
        if (!$request->user()->hasRole('super_admin') && $event->client_id !== $request->user()->id) {
            abort(403, 'Unauthorized');
        }

        return response()->json(['event' => $event->load('client')]);
    }

    public function update(Request $request, Event $event)
    {
        if (!$request->user()->hasRole('super_admin') && $event->client_id !== $request->user()->id) {
            abort(403, 'Unauthorized');
        }

        $validated = $request->validate([
            'event_name' => 'sometimes|string|max:255',
            'event_type' => 'nullable|string',
            'theme' => 'nullable|string',
            'venue' => 'nullable|string',
            'event_date' => 'nullable|date',
            'description' => 'nullable|string',
            'package' => 'nullable|string|in:basic,premium,luxury',
        ]);

        $event->update($validated);

        return response()->json(['event' => $event]);
    }

    public function destroy(Request $request, Event $event)
    {
        if (!$request->user()->hasRole('super_admin') && $event->client_id !== $request->user()->id) {
            abort(403, 'Unauthorized');
        }

        $event->delete();

        return response()->json(['message' => 'Event deleted']);
    }

    public function duplicate(Request $request, Event $event)
    {
        if (!$request->user()->hasRole('super_admin') && $event->client_id !== $request->user()->id) {
            abort(403, 'Unauthorized');
        }

        $new_event = $event->replicate();
        $new_event->event_name = $event->event_name . ' (Copy)';
        $new_event->slug = Str::slug($new_event->event_name) . '-' . Str::random(5);
        $new_event->status = 'draft';
        $new_event->save();

        return response()->json(['event' => $new_event], 201);
    }

    public function publish(Request $request, Event $event)
    {
        if (!$request->user()->hasRole('super_admin') && $event->client_id !== $request->user()->id) {
            abort(403, 'Unauthorized');
        }

        $event->update(['status' => 'published']);
        return response()->json(['event' => $event]);
    }

    public function archive(Request $request, Event $event)
    {
        if (!$request->user()->hasRole('super_admin') && $event->client_id !== $request->user()->id) {
            abort(403, 'Unauthorized');
        }

        $event->update(['status' => 'archived']);
        return response()->json(['event' => $event]);
    }
}
