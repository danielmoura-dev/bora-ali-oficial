<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class EventController extends Controller
{
    private array $brazilianStates = [
        'AC',
        'AL',
        'AP',
        'AM',
        'BA',
        'CE',
        'DF',
        'ES',
        'GO',
        'MA',
        'MT',
        'MS',
        'MG',
        'PA',
        'PB',
        'PR',
        'PE',
        'PI',
        'RJ',
        'RN',
        'RS',
        'RO',
        'RR',
        'SC',
        'SP',
        'SE',
        'TO',
    ];

    public function create()
    {
        return view('events.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'min:5', 'max:150'],
            'description' => ['required', 'string', 'min:20'],
            'venue_name' => ['required', 'string', 'max:150'],
            'venue_address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['required', Rule::in($this->brazilianStates)],
            'starts_at' => ['required', 'date', 'after:now'],
            'ends_at' => ['required', 'date', 'after:starts_at'],
            'is_free' => ['sometimes', 'boolean'],
            'cover_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'payment_provider' => ['required', 'in:mercadopago,pagarme'],
            'payment_mode' => ['required', 'in:split,direct'],
            'payment_methods' => ['required', 'array', 'min:1'],
            'payment_methods.*' => ['in:pix,credit_card'],
        ]);

        $coverPath = null;
        if ($request->hasFile('cover_image')) {
            $coverPath = $request->file('cover_image')
                ->store('events/covers', 'public');
        }

        $event = Event::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'slug' => Event::generateSlug($validated['title']),
            'description' => $validated['description'],
            'venue_name' => $validated['venue_name'],
            'venue_address' => $validated['venue_address'],
            'city' => $validated['city'],
            'state' => $validated['state'],
            'starts_at' => $validated['starts_at'],
            'ends_at' => $validated['ends_at'],
            'is_free' => $request->boolean('is_free'),
            'cover_image' => $coverPath,
            'status' => 'draft',
            'payment_provider' => $validated['payment_provider'],
            'payment_mode' => $validated['payment_mode'],
            'payment_methods' => $validated['payment_methods'],
        ]);

        return redirect()->route('events.show', $event->slug)
            ->with('status', 'Evento criado! Revise e publique quando estiver pronto.');
    }

    public function show(string $slug)
    {
        $event = Event::where('slug', $slug)
            ->with(['ticketTypes.batches', 'organizer'])
            ->firstOrFail();

        return view('events.show', compact('event'));
    }

    public function publish(string $slug)
    {
        $event = Event::where('slug', $slug)->firstOrFail();

        Gate::authorize('publish', $event);

        $event->forceFill(['status' => 'published'])->save();

        return redirect()->route('events.show', $event->slug)
            ->with('status', 'Evento publicado com sucesso!');
    }

    public function my()
    {
        $events = Event::where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->get();

        return view('events.my', compact('events'));
    }
}