<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'icon',
        'description',
        'is_active',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function fields()
    {
        return $this->hasMany(CategoryField::class)->orderBy('sort_order');
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }
}
