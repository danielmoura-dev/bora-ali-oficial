<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Services\CheckinService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CheckinController extends Controller
{
    public function __construct(private CheckinService $checkinService) {}

    public function index(string $slug)
    {
        $event = Event::where('slug', $slug)->firstOrFail();
        Gate::authorize('update', $event);

        return view('checkin.index', compact('event'));
    }

    public function scan(Request $request, string $slug)
    {
        $event = Event::where('slug', $slug)->firstOrFail();
        Gate::authorize('update', $event);

        $request->validate([
            'ticket_code' => ['required', 'string'],
        ]);

        $result = $this->checkinService->scan($event, $request->ticket_code);

        $httpStatus = match ($result['status']) {
            'success'            => 200,
            'already_checked_in' => 409,
            'wrong_event'        => 422,
            'not_found'          => 404,
            default              => 400,
        };

        return response()->json($result, $httpStatus);
    }

    public function stats(string $slug)
    {
        $event = Event::where('slug', $slug)->firstOrFail();
        Gate::authorize('update', $event);

        $stats = $this->checkinService->stats($event);

        return response()->json([
            'total'      => $stats['total'],
            'checked_in' => $stats['checkedIn'],
            'remaining'  => $stats['remaining'],
            'percentage' => $stats['percentage'],
        ]);
    }
}