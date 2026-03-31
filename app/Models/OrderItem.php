<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'ticket_batch_id',
        'ticket_type_id',
        'quantity',
        'unit_price',
        'subtotal',
        'ticket_code',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function batch()
    {
        return $this->belongsTo(TicketBatch::class, 'ticket_batch_id');
    }

    public function ticketType()
    {
        return $this->belongsTo(TicketType::class);
    }

    public static function generateTicketCode(): string
    {
        do {
            $code = strtoupper(Str::random(4)) . '-' . strtoupper(Str::random(4));
        } while (static::where('ticket_code', $code)->exists());

        return $code;
    }

    public function checkin()
    {
        return $this->hasOne(Checkin::class);
    }

    public function isCheckedIn(): bool
    {
        return $this->checkin()->exists();
    }
}