<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Checkin extends Model
{
    protected $fillable = [
        'order_item_id',
        'event_id',
        'checked_in_by',
        'ticket_code',
        'checked_in_at',
    ];

    protected function casts(): array
    {
        return [
            'checked_in_at' => 'datetime',
        ];
    }

    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function operator()
    {
        return $this->belongsTo(User::class, 'checked_in_by');
    }
}