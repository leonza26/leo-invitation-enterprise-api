<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        $query = Event::query();
        if (!$user->hasRole('super_admin') && !$user->hasRole('operator')) {
            $query->where('client_id', $user->id);
        }

        $events = $query->get();
        
        $totalEvents = $events->count();
        $publishedEvents = $events->where('status', 'published')->count();
        
        // Mock analytics data, real data would join RSVPs and Guests
        // Assuming rsvps table exists from Phase 1, we could count them.
        
        return response()->json([
            'metrics' => [
                'total_events' => $totalEvents,
                'published_events' => $publishedEvents,
                'total_guests' => $totalEvents * 150, // mock data
                'rsvps_received' => $totalEvents * 120, // mock data
                'vip_guests' => $totalEvents * 15, // mock data
            ],
            'recent_events' => $events->take(5)
        ]);
    }
}
