<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AffiliateClick;
use App\Models\Category;
use App\Models\Product;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index(): View
    {
        $totalProducts = Product::count();
        $totalCategories = Category::count();

        $totalClicks = AffiliateClick::count();
        $clicksToday = AffiliateClick::query()->where('clicked_at', '>=', now()->startOfDay())->count();
        $clicksThisWeek = AffiliateClick::query()->where('clicked_at', '>=', now()->startOfWeek())->count();
        $clicksThisMonth = AffiliateClick::query()->where('clicked_at', '>=', now()->startOfMonth())->count();

        $topProducts = Product::query()
            ->with('category')
            ->withCount('affiliateClicks')
            ->orderByDesc('affiliate_clicks_count')
            ->orderByDesc('id')
            ->take(5)
            ->get()
            ->filter(fn (Product $product) => $product->affiliate_clicks_count > 0)
            ->values();

        $trendingProduct = $topProducts->first();

        $recentProducts = Product::query()
            ->with(['category', 'primaryImage'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', [
            'totalProducts' => $totalProducts,
            'totalCategories' => $totalCategories,
            'totalClicks' => $totalClicks,
            'clicksToday' => $clicksToday,
            'clicksThisWeek' => $clicksThisWeek,
            'clicksThisMonth' => $clicksThisMonth,
            'topProducts' => $topProducts,
            'trendingProduct' => $trendingProduct,
            'recentProducts' => $recentProducts,
        ]);
    }
}
