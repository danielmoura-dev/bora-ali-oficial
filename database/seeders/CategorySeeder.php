<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        Category::create([
            'name'        => 'Festa, Festival ou Show',
            'slug'        => 'festa-festival-show',
            'icon'        => '🎉',
            'is_active'   => true,
            'sort_order'  => 1,
        ]);
    }
}
