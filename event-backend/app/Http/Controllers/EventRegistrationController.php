<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventRegistrationController extends Controller
{
    public function register(Event $event)
    {
        if (Auth::user()->is_admin) {
            return response()->json(['message' => 'Organizers cannot register for events'], 403);
        }

        // Check if already registered
        if ($event->attendees()->where('user_id', Auth::id())->exists()) {
            return response()->json(['message' => 'Already registered for this event'], 409);
        }

        // Check ticket availability
        if ($event->available_tickets_count <= 0) {
            return response()->json(['message' => 'No tickets available'], 400);
        }

        $event->attendees()->attach(Auth::id(), ['status' => 'pending']);

        return response()->json(['message' => 'Registration submitted successfully']);
    }

    public function updateRegistrationStatus(Request $request, Event $event, User $user)
    {
        if (!Auth::user()->is_admin || $event->user_id !== Auth::id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'status' => 'required|in:approved,rejected'
        ]);

        $event->attendees()->updateExistingPivot($user->id, [
            'status' => $request->status
        ]);

        return response()->json(['message' => 'Registration status updated successfully']);
    }

    public function getUserRegistrations(Request $request)
    {
        try {
            $user = $request->user();
            if (!$user) {
                return response()->json(['message' => 'Unauthenticated'], 401);
            }
            // Use the registrations() relationship defined in the User model
            $registrations = $user->registrations()->get();
            return response()->json(['data' => $registrations]);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Failed to load registrations',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}