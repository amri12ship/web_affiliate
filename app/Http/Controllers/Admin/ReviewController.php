<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    /**
     * Sorts supported by the review listing.
     *
     * @var array<string, string>
     */
    public const SORT_OPTIONS = [
        'rating_desc' => 'Rating Tertinggi',
        'rating_asc' => 'Rating Terendah',
        'sold_desc' => 'Terlaris',
        'clicks_desc' => 'Paling Banyak Diklik',
        'newest' => 'Terbaru',
    ];

    /**
     * Display the product performance and rating review.
     */
    public function index(Request $request): View
    {
        $sort = $request->string('sort')->toString();
        $sort = array_key_exists($sort, self::SORT_OPTIONS) ? $sort : 'rating_desc';

        $query = Product::query()
            ->with(['category', 'primaryImage'])
            ->withCount('affiliateClicks')
            ->when($request->filled('q'), function ($query) use ($request): void {
                $query->where('name', 'like', '%'.$request->string('q').'%');
            })
            ->when($request->filled('category'), function ($query) use ($request): void {
                $query->where('category_id', $request->integer('category'));
            });

        match ($sort) {
            'rating_asc' => $query->orderBy('rating')->orderByDesc('id'),
            'sold_desc' => $query->orderByDesc('sold_count')->orderByDesc('id'),
            'clicks_desc' => $query->orderByDesc('affiliate_clicks_count')->orderByDesc('id'),
            'newest' => $query->latest(),
            default => $query->orderByDesc('rating')->orderByDesc('id'),
        };

        $products = $query->paginate(12)->withQueryString();

        $averageRating = round((float) Product::query()->avg('rating'), 2);
        $ratedProducts = Product::query()->where('rating', '>', 0)->count();

        $ratingBreakdown = collect([5, 4, 3, 2, 1])->map(function (int $star): array {
            return [
                'star' => $star,
                'count' => Product::query()->where('rating', '>=', $star)->where('rating', '<', $star + 1)->count(),
            ];
        });

        $bestRated = Product::query()
            ->with('category')
            ->where('rating', '>', 0)
            ->orderByDesc('rating')
            ->orderByDesc('sold_count')
            ->first();

        $mostSold = Product::query()
            ->where('sold_count', '>', 0)
            ->orderByDesc('sold_count')
            ->first();

        return view('admin.review.index', [
            'products' => $products,
            'categories' => Category::orderBy('name')->get(),
            'sortOptions' => self::SORT_OPTIONS,
            'sort' => $sort,
            'averageRating' => $averageRating,
            'ratedProducts' => $ratedProducts,
            'totalProducts' => Product::count(),
            'ratingBreakdown' => $ratingBreakdown,
            'bestRated' => $bestRated,
            'mostSold' => $mostSold,
        ]);
    }
}
