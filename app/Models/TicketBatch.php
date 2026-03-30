<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_type_id', 'name', 'quantity',
        'quantity_sold', 'price', 'starts_at',
        'ends_at', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at'   => 'datetime',
            'is_active' => 'boolean',
        ];
    }

    public function ticketType()
    {
        return $this->belongsTo(TicketType::class);
    }

    public function remainingQuantity(): int
    {
        return $this->quantity - $this->quantity_sold;
    }

    public function hasAvailability(): bool
    {
        return $this->remainingQuantity() > 0;
    }

    public function formattedPrice(): string
    {
        return 'R$ ' . number_format($this->price / 100, 2, ',', '.');
    }
}