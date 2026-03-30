<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->string('q')->trim()->toString();

        $currentEvents = Event::published()
            ->current()
            ->when($query, fn ($q) => $q->search($query))
            ->orderBy('starts_at')
            ->get();

        $finishedEvents = Event::published()
            ->finished()
            ->when($query, fn ($q) => $q->search($query))
            ->orderByDesc('ends_at')
            ->limit(12)
            ->get();

        return view('home', compact('currentEvents', 'finishedEvents', 'query'));
    }
}