<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InertiaController extends Controller
{
    public function show()
    {
        return Inertia::render('Event/Show', [
            'events' => Event::all()->map(function ($event) {
                return $event->only(
                    'id',
                    'title',
                    'start_date',
                    'description'
                );
            }),
        ]);
    }
    }
