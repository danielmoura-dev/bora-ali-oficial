<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'event_id', 'reference',
        'subtotal', 'platform_fee', 'total',
        'status', 'payment_method',
        'payment_id', 'payment_metadata',
    ];

    protected function casts(): array
    {
        return [
            'payment_metadata' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public static function generateReference(): string
    {
        do {
            $ref = 'BA-' . strtoupper(Str::random(8));
        } while (static::where('reference', $ref)->exists());

        return $ref;
    }

    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function formattedTotal(): string
    {
        return 'R$ ' . number_format($this->total / 100, 2, ',', '.');
    }
}