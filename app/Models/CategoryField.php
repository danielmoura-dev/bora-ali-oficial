<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CategoryField extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'label',
        'type',
        'options',
        'placeholder',
        'required',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'options'  => 'array',
            'required' => 'boolean',
        ];
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function eventFieldValues()
    {
        return $this->hasMany(EventFieldValue::class);
    }
}
