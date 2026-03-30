<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketType extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id', 'name', 'description',
        'is_half_price', 'sort_order',
    ];

    protected function casts(): array
    {
        return ['is_half_price' => 'boolean'];
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function batches()
    {
        return $this->hasMany(TicketBatch::class)->orderBy('starts_at');
    }

    public function activeBatch(): ?TicketBatch
    {
        return $this->batches()
            ->where('is_active', true)
            ->where('quantity_sold', '<', \DB::raw('quantity'))
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>=', now());
            })
            ->first();
    }

    public function isAvailable(): bool
    {
        return $this->activeBatch() !== null;
    }
}