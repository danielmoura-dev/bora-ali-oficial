<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Event;
use App\Models\EventFieldValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class EventController extends Controller
{
    private array $brazilianStates = [
        'AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA',
        'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN',
        'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO',
    ];

    public function create()
    {
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();

        return view('events.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'               => ['required', 'string', 'min:5', 'max:150'],
            'description'         => ['required', 'string', 'min:20'],
            'category_id'         => ['nullable', 'exists:categories,id'],
            'venue_name'          => ['required', 'string', 'max:150'],
            'venue_address'       => ['required', 'string', 'max:255'],
            'city'                => ['required', 'string', 'max:100'],
            'state'               => ['required', Rule::in($this->brazilianStates)],
            'starts_at'           => ['required', 'date', 'after:now'],
            'ends_at'             => ['required', 'date', 'after:starts_at'],
            'is_free'             => ['sometimes', 'boolean'],
            'absorb_service_fee'  => ['sometimes', 'boolean'],
            'ticket_nomenclature' => ['sometimes', Rule::in(['ingresso', 'inscricao'])],
            'cover_image'         => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:3072'],
            'payment_provider'    => ['required', 'in:mercadopago,pagarme'],
            'payment_mode'        => ['required', 'in:split,direct'],
            'payment_methods'     => ['required', 'array', 'min:1'],
            'payment_methods.*'   => ['in:pix,credit_card'],
        ]);

        $coverPath = null;
        if ($request->hasFile('cover_image')) {
            $coverPath = $request->file('cover_image')
                ->store('events/covers', 'public');
        }

        $event = Event::create([
            'user_id'             => Auth::id(),
            'category_id'         => $validated['category_id'] ?? null,
            'title'               => $validated['title'],
            'slug'                => Event::generateSlug($validated['title']),
            'description'         => $validated['description'],
            'venue_name'          => $validated['venue_name'],
            'venue_address'       => $validated['venue_address'],
            'city'                => $validated['city'],
            'state'               => $validated['state'],
            'starts_at'           => $validated['starts_at'],
            'ends_at'             => $validated['ends_at'],
            'is_free'             => $request->boolean('is_free'),
            'absorb_service_fee'  => $request->boolean('absorb_service_fee'),
            'ticket_nomenclature' => $validated['ticket_nomenclature'] ?? 'ingresso',
            'cover_image'         => $coverPath,
            'status'              => 'draft',
            'payment_provider'    => $validated['payment_provider'],
            'payment_mode'        => $validated['payment_mode'],
            'payment_methods'     => $validated['payment_methods'],
        ]);

        // Salvar campos extras da categoria
        if ($event->category_id) {
            $category = Category::with('fields')->find($event->category_id);
            foreach ($category->fields as $field) {
                $value = $request->input("fields.{$field->name}");
                if ($value !== null && $value !== '') {
                    EventFieldValue::create([
                        'event_id'          => $event->id,
                        'category_field_id' => $field->id,
                        'value'             => $value,
                    ]);
                }
            }
        }

        return redirect()->route('events.show', $event->slug)
            ->with('status', 'Evento criado! Revise e publique quando estiver pronto.');
    }

    public function show(string $slug)
    {
        $event = Event::where('slug', $slug)
            ->with(['ticketTypes.batches', 'organizer', 'category', 'fieldValues.field'])
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
