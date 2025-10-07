<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::with('user');
        
        // Search filters
        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }
        
        if ($request->has('category') && $request->category !== 'all') {
            $query->where('category', $request->category);
        }
        
        if ($request->has('date')) {
            $query->whereDate('event_date', $request->date);
        }
        
        if ($request->has('location')) {
            $query->where('location', 'like', '%' . $request->location . '%');
        }

        $events = $query->orderBy('event_date', 'asc')->paginate(10);

        return response()->json($events);
    }

    public function store(Request $request)
    {
        if (!Auth::user()->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'event_date' => 'required|date',
            'location' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'category' => 'required|in:sports,personal,family',
            'available_tickets' => 'required|integer|min:1',
            'price' => 'required|numeric|min:0',
        ]);

        $event = Auth::user()->events()->create($validated);

        return response()->json($event, 201);
    }

    public function show(Event $event)
    {
        $event->load('user', 'attendees');
        return response()->json($event);
    }

    public function update(Request $request, Event $event)
    {
        if (!Auth::user()->is_admin || $event->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'event_date' => 'sometimes|date',
            'location' => 'sometimes|string|max:255',
            'address' => 'sometimes|string|max:500',
            'category' => 'sometimes|in:sports,personal,family',
            'available_tickets' => 'sometimes|integer|min:1',
            'price' => 'sometimes|numeric|min:0',
        ]);

        $event->update($validated);

        return response()->json($event);
    }

    public function destroy(Event $event)
    {
        if (!Auth::user()->is_admin || $event->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $event->delete();

        return response()->json(['message' => 'Event deleted successfully']);
    }

    public function userEvents()
    {
        $events = Auth::user()->registeredEvents()
            ->with('user')
            ->orderBy('event_date', 'asc')
            ->get();

        return response()->json($events);
    }
}