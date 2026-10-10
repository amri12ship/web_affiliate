<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Display the public homepage.
     */
    public function index(): View
    {
        $categories = Category::query()
            ->where('status', Category::STATUS_ACTIVE)
            ->withCount('products')
            ->orderBy('name')
            ->get();

        $newest = fn () => Product::query()
            ->where('status', Product::STATUS_ACTIVE)
            ->with(['category', 'primaryImage'])
            ->latest();

        $trendingProducts = $newest()->where('is_trending', true)->take(8)->get();
        $viralProducts = $newest()->where('is_viral', true)->take(8)->get();
        $recommendedProducts = $newest()->where('is_recommended', true)->take(8)->get();
        $latestProducts = $newest()->take(8)->get();

        return view('home', compact(
            'categories',
            'trendingProducts',
            'viralProducts',
            'recommendedProducts',
            'latestProducts',
        ));
    }
}
