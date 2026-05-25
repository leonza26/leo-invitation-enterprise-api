<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Guest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class GuestController extends Controller
{
    /**
     * Get guests for a specific event (with search, filter, pagination).
     */
    public function index(Request $request, $eventId)
    {
        $event = Event::where('id', $eventId)->where('client_id', $request->user()->id)->first();
        if (!$event && !$request->user()->hasRole('super_admin')) {
            return response()->json(['message' => 'Unauthorized or event not found'], 403);
        }

        $query = Guest::where('event_id', $eventId);

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('guest_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        if ($request->has('vip_status')) {
            $query->where('vip_status', $request->boolean('vip_status'));
        }

        if ($request->has('attendance_status')) {
            $query->where('attendance_status', $request->attendance_status);
        }

        if ($request->has('category')) {
            $query->where('category', $request->category);
        }

        $guests = $query->paginate($request->get('per_page', 10));

        return response()->json($guests);
    }

    /**
     * Store a newly created guest.
     */
    public function store(Request $request, $eventId)
    {
        $event = Event::findOrFail($eventId);
        
        $validator = Validator::make($request->all(), [
            'guest_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'vip_status' => 'boolean',
            'category' => 'nullable|string',
            'seat_number' => 'nullable|string',
            'attendance_status' => 'in:attending,not attending,pending',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $guest = new Guest($validator->validated());
        $guest->event_id = $event->id;
        $guest->save();

        return response()->json(['message' => 'Guest added successfully', 'guest' => $guest], 201);
    }

    /**
     * Update the specified guest.
     */
    public function update(Request $request, $eventId, $id)
    {
        $guest = Guest::where('event_id', $eventId)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'guest_name' => 'sometimes|required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'vip_status' => 'boolean',
            'category' => 'nullable|string',
            'seat_number' => 'nullable|string',
            'attendance_status' => 'in:attending,not attending,pending',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $guest->update($validator->validated());

        return response()->json(['message' => 'Guest updated successfully', 'guest' => $guest]);
    }

    /**
     * Remove the specified guest.
     */
    public function destroy($eventId, $id)
    {
        $guest = Guest::where('event_id', $eventId)->findOrFail($id);
        $guest->delete();

        return response()->json(['message' => 'Guest deleted successfully']);
    }

    /**
     * Generate WhatsApp Link for the guest.
     */
    public function whatsappLink($eventId, $id)
    {
        $guest = Guest::with('event')->where('event_id', $eventId)->findOrFail($id);
        
        if (!$guest->phone) {
            return response()->json(['message' => 'Guest does not have a phone number'], 400);
        }

        $frontendUrl = env('FRONTEND_URL', 'http://localhost:5173');
        // The public link for the guest RSVP
        $invitationLink = "{$frontendUrl}/v/{$guest->event->slug}/guest/{$guest->qr_code}";
        
        $message = "Hello {$guest->guest_name},\n\nYou are invited to {$guest->event->event_name}!\nPlease view your invitation and confirm your attendance here:\n{$invitationLink}\n\nThank you.";
        $encodedMessage = urlencode($message);

        // Format phone to basic numeric, usually replacing leading 0 with 62 etc, but we just leave it for now or strip non-digits.
        $phone = preg_replace('/[^0-9]/', '', $guest->phone);

        $waLink = "https://wa.me/{$phone}?text={$encodedMessage}";

        return response()->json(['link' => $waLink]);
    }

    /**
     * Bulk Upload/Import guests via CSV or JSON array.
     */
    public function bulkImport(Request $request, $eventId)
    {
        $event = Event::findOrFail($eventId);
        
        $request->validate([
            'guests' => 'required|array',
            'guests.*.guest_name' => 'required|string',
        ]);

        $imported = 0;
        foreach ($request->guests as $guestData) {
            $guest = new Guest();
            $guest->event_id = $event->id;
            $guest->guest_name = $guestData['guest_name'];
            $guest->phone = $guestData['phone'] ?? null;
            $guest->email = $guestData['email'] ?? null;
            $guest->category = $guestData['category'] ?? null;
            $guest->vip_status = filter_var($guestData['vip_status'] ?? false, FILTER_VALIDATE_BOOLEAN);
            $guest->save();
            $imported++;
        }

        return response()->json(['message' => "Successfully imported {$imported} guests."]);
    }

    // --- PUBLIC ENDPOINTS (No Auth Required) ---

    /**
     * Public access to a guest's details using their unique qr_code/token.
     */
    public function publicShow($slug, $qrCode)
    {
        $event = Event::where('slug', $slug)->firstOrFail();
        $guest = Guest::where('event_id', $event->id)->where('qr_code', $qrCode)->firstOrFail();

        return response()->json([
            'event' => $event,
            'guest' => $guest,
        ]);
    }

    /**
     * Public RSVP update by guest.
     */
    public function publicUpdateRsvp(Request $request, $slug, $qrCode)
    {
        $event = Event::where('slug', $slug)->firstOrFail();
        $guest = Guest::where('event_id', $event->id)->where('qr_code', $qrCode)->firstOrFail();

        $validator = Validator::make($request->all(), [
            'attendance_status' => 'required|in:attending,not attending,pending',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $guest->attendance_status = $request->attendance_status;
        $guest->save();

        return response()->json(['message' => 'RSVP updated successfully', 'guest' => $guest]);
    }
}
