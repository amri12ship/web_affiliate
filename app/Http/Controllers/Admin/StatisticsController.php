<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AffiliateClick;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StatisticsController extends Controller
{
    /**
     * Display affiliate click statistics.
     */
    public function index(): View
    {
        $totalClicks = AffiliateClick::count();
        $clicksToday = AffiliateClick::query()->where('clicked_at', '>=', now()->startOfDay())->count();
        $clicksThisWeek = AffiliateClick::query()->where('clicked_at', '>=', now()->startOfWeek())->count();
        $clicksThisMonth = AffiliateClick::query()->where('clicked_at', '>=', now()->startOfMonth())->count();

        $topProducts = Product::query()
            ->with('category')
            ->withCount('affiliateClicks')
            ->orderByDesc('affiliate_clicks_count')
            ->orderByDesc('id')
            ->take(10)
            ->get();

        $categoryStats = AffiliateClick::query()
            ->join('products', 'products.id', '=', 'affiliate_clicks.product_id')
            ->join('categories', 'categories.id', '=', 'products.category_id')
            ->select('categories.id', 'categories.name', DB::raw('COUNT(*) as clicks'))
            ->groupBy('categories.id', 'categories.name')
            ->orderByDesc('clicks')
            ->get();

        $dailyClicks = $this->dailyClicks(30);
        $maxDailyClicks = max(1, (int) $dailyClicks->max('total'));

        $totalClicksCount = (int) $topProducts->sum('affiliate_clicks_count');
        $productsWithClicks = $topProducts->where('affiliate_clicks_count', '>', 0)->count();

        return view('admin.statistics.index', [
            'totalClicks' => $totalClicks,
            'clicksToday' => $clicksToday,
            'clicksThisWeek' => $clicksThisWeek,
            'clicksThisMonth' => $clicksThisMonth,
            'topProducts' => $topProducts,
            'categoryStats' => $categoryStats,
            'dailyClicks' => $dailyClicks,
            'maxDailyClicks' => $maxDailyClicks,
            'totalProducts' => Product::count(),
            'totalCategories' => Category::count(),
            'productsWithClicks' => $productsWithClicks,
            'averageClicks' => $totalClicksCount > 0 && $productsWithClicks > 0
                ? round($totalClicksCount / $productsWithClicks, 1)
                : 0,
        ]);
    }

    /**
     * Build the daily click series for the given number of days.
     *
     * @return Collection<int, array{date: string, label: string, total: int}>
     */
    protected function dailyClicks(int $days): Collection
    {
        $since = now()->subDays($days - 1)->startOfDay();

        $counts = AffiliateClick::query()
            ->where('clicked_at', '>=', $since)
            ->select(DB::raw('DATE(clicked_at) as day'), DB::raw('COUNT(*) as total'))
            ->groupBy('day')
            ->pluck('total', 'day');

        return collect(range($days - 1, 0))->map(function (int $offset) use ($counts): array {
            $date = now()->subDays($offset);

            return [
                'date' => $date->toDateString(),
                'label' => $date->format('d M'),
                'total' => (int) ($counts[$date->toDateString()] ?? 0),
            ];
        });
    }
}
