<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;

class OrganizerPublicController extends Controller
{
    public function showByUsername(string $username)
    {
        $organizer = User::where('username', $username)->firstOrFail();

        return $this->renderProfile($organizer);
    }

    public function showById(int $id)
    {
        $organizer = User::findOrFail($id);

        // Se tem username, redireciona para a URL canônica
        if ($organizer->username) {
            return redirect()->route('organizer.public', $organizer->username);
        }

        return $this->renderProfile($organizer);
    }

    private function renderProfile(User $organizer)
    {
        $currentEvents = Event::where('user_id', $organizer->id)
            ->published()
            ->current()
            ->orderBy('starts_at')
            ->get();

        $finishedEvents = Event::where('user_id', $organizer->id)
            ->published()
            ->finished()
            ->orderByDesc('ends_at')
            ->limit(6)
            ->get();

        $totalEvents = Event::where('user_id', $organizer->id)
            ->published()
            ->count();

        return view('organizer.public', compact(
            'organizer',
            'currentEvents',
            'finishedEvents',
            'totalEvents',
        ));
    }
}