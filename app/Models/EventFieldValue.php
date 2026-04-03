<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventFieldValue extends Model
{
    protected $fillable = [
        'event_id',
        'category_field_id',
        'value',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function field()
    {
        return $this->belongsTo(CategoryField::class, 'category_field_id');
    }
}
