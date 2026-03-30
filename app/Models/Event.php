<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'description',
        'cover_image',
        'venue_name',
        'venue_address',
        'city',
        'state',
        'starts_at',
        'ends_at',
        'status',
        'is_free',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'is_free' => 'boolean',
        ];
    }

    // ── Relacionamentos ───────────────────────────────────────

    public function organizer()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function ticketTypes()
    {
        return $this->hasMany(TicketType::class)->orderBy('sort_order');
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function hasAvailableTickets(): bool
    {
        return $this->ticketTypes->some(fn($t) => $t->isAvailable());
    }

    // ── Scopes ────────────────────────────────────────────────

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    public function scopeCurrent(Builder $query): Builder
    {
        return $query->where('starts_at', '>=', now())
            ->orWhere(function ($q) {
                $q->where('starts_at', '<=', now())
                    ->where('ends_at', '>=', now());
            });
    }

    public function scopeFinished(Builder $query): Builder
    {
        return $query->where('ends_at', '<', now());
    }

    public function scopeSearch(Builder $query, string $term): Builder
    {
        return $query->where(function ($q) use ($term) {
            $q->where('title', 'like', "%{$term}%")
                ->orWhere('description', 'like', "%{$term}%")
                ->orWhere('city', 'like', "%{$term}%")
                ->orWhere('venue_name', 'like', "%{$term}%");
        });
    }

    // ── Helpers ───────────────────────────────────────────────

    public static function generateSlug(string $title): string
    {
        $slug = Str::slug($title);
        $count = static::where('slug', 'like', "{$slug}%")->count();

        return $count > 0 ? "{$slug}-{$count}" : $slug;
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function isFinished(): bool
    {
        return $this->ends_at->isPast();
    }

    public function isCurrent(): bool
    {
        return $this->starts_at->isFuture()
            || ($this->starts_at->isPast() && $this->ends_at->isFuture());
    }
}