<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;

class CategoryController extends Controller
{
    public function display() {
        $categories = Category::query()->orderBy('name')->get();
        return view('allcategories', compact('categories'));
    }

    public function show(string $slug)
    {
        $category = Category::query()->where('slug', $slug)->firstOrFail();
        $categories = Category::query()->orderBy('name')->get();

        $items = Item::query()
            ->where('category_id', $category->id)
            ->orderByDesc('id')
            ->limit(36)
            ->get();

        $hero = [
            'title' => $category->name,
            'subtitle' => 'Fresh picks for you',
            'description' => null,
            'image' => $category->hero_image_url,
        ];

        return view('category', compact('category', 'categories', 'items', 'hero'));
    }
}