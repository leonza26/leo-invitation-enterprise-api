<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\GuestBook;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GuestBookController extends Controller
{
    /**
     * Get guestbook entries for an event (public).
     */
    public function index($slug)
    {
        $event = Event::where('slug', $slug)->firstOrFail();
        $entries = GuestBook::where('event_id', $event->id)
            ->where('is_approved', true)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($entries);
    }

    /**
     * Store a new guestbook entry (public).
     */
    public function store(Request $request, $slug)
    {
        $event = Event::where('slug', $slug)->firstOrFail();

        $validator = Validator::make($request->all(), [
            'guest_name' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $entry = new GuestBook([
            'guest_name' => $request->guest_name,
            'message' => $request->message,
            'is_approved' => true, // default approved for now
        ]);
        
        $entry->event_id = $event->id;
        $entry->save();

        return response()->json(['message' => 'Wish sent successfully', 'entry' => $entry], 201);
    }

    /**
     * Delete a guestbook entry (requires auth).
     */
    public function destroy(Request $request, $eventId, $id)
    {
        // Must be owner or admin (basic check, could use policy)
        $event = Event::where('id', $eventId)->where('client_id', $request->user()->id)->first();
        if (!$event && !$request->user()->hasRole('super_admin')) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $entry = GuestBook::where('event_id', $eventId)->findOrFail($id);
        $entry->delete();

        return response()->json(['message' => 'Entry deleted successfully']);
    }
}
