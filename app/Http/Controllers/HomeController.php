<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::query()->orderBy('name')->get();

        $featuredItems = Item::query()
            ->where('is_featured', 1)
            ->orderByDesc('id')
            ->limit(12)
            ->get();

        $clearanceItems = Item::query()
            ->where('is_clearance', 1)
            ->orderByDesc('id')
            ->limit(12)
            ->get();

        // Default hero image (you can change to a DB-driven value later)
        $hero = [
            'title' => 'Welcome to Super Mart',
            'subtitle' => 'Fresh Groceries, Delivered Fast',
            'description' => 'Shop fruits, dairy, pastries and more — all in one place.',
            'image' => asset('images/hero-groceries.png'),
        ];

        return view('welcome', compact('categories', 'featuredItems', 'clearanceItems', 'hero'));
    }
}

