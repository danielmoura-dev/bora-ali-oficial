<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function fields(string $slug): JsonResponse
    {
        $category = Category::where('slug', $slug)
            ->where('is_active', true)
            ->with('fields')
            ->firstOrFail();

        return response()->json([
            'category' => [
                'name' => $category->name,
                'icon' => $category->icon,
            ],
            'fields' => $category->fields->map(fn ($field) => [
                'name'        => $field->name,
                'label'       => $field->label,
                'type'        => $field->type,
                'required'    => $field->required,
                'placeholder' => $field->placeholder,
                'options'     => $field->options,
            ]),
        ]);
    }
}
