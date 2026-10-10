<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CatalogController extends Controller
{
    /**
     * Available sorting options for the catalog.
     *
     * @var list<string>
     */
    public const SORT_OPTIONS = ['newest', 'cheapest', 'expensive', 'rating', 'popular'];

    /**
     * Display the public product catalog.
     */
    public function index(Request $request): View
    {
        $sort = (string) $request->query('sort', 'newest');

        $filters = [
            'search' => trim((string) $request->query('search', '')),
            'category' => $request->filled('category') ? $request->integer('category') : null,
            'min_price' => $request->filled('min_price') ? (float) $request->query('min_price') : null,
            'max_price' => $request->filled('max_price') ? (float) $request->query('max_price') : null,
            'min_rating' => $request->filled('min_rating') ? (float) $request->query('min_rating') : null,
            'sort' => in_array($sort, self::SORT_OPTIONS, true) ? $sort : 'newest',
        ];

        $products = Product::query()
            ->where('status', Product::STATUS_ACTIVE)
            ->with(['category', 'primaryImage'])
            ->when($filters['search'] !== '', function (Builder $query) use ($filters): void {
                $term = '%'.$filters['search'].'%';

                $query->where(function (Builder $query) use ($term): void {
                    $query->where('name', 'like', $term)
                        ->orWhere('description', 'like', $term);
                });
            })
            ->when($filters['category'] !== null, fn (Builder $query) => $query->where('category_id', $filters['category']))
            ->when($filters['min_price'] !== null, fn (Builder $query) => $query->where('price', '>=', $filters['min_price']))
            ->when($filters['max_price'] !== null, fn (Builder $query) => $query->where('price', '<=', $filters['max_price']))
            ->when($filters['min_rating'] !== null, fn (Builder $query) => $query->where('rating', '>=', $filters['min_rating']))
            ->when(true, fn (Builder $query) => $this->applySorting($query, $filters['sort']))
            ->paginate(12)
            ->withQueryString();

        $categories = Category::query()
            ->where('status', Category::STATUS_ACTIVE)
            ->orderBy('name')
            ->get();

        return view('products.index', compact('products', 'categories', 'filters'));
    }

    /**
     * Apply the requested sorting to the catalog query.
     */
    private function applySorting(Builder $query, string $sort): void
    {
        match ($sort) {
            'cheapest' => $query->orderBy('price'),
            'expensive' => $query->orderByDesc('price'),
            'rating' => $query->orderByDesc('rating'),
            'popular' => $query->orderByDesc('sold_count'),
            default => $query->latest(),
        };
    }

    /**
     * Display a single public product detail page.
     */
    public function show(Product $product): View
    {
        abort_unless($product->isActive(), 404);

        $product->load(['category', 'images']);

        $relatedProducts = Product::query()
            ->where('status', Product::STATUS_ACTIVE)
            ->where('category_id', $product->category_id)
            ->whereKeyNot($product->getKey())
            ->with(['category', 'primaryImage'])
            ->latest()
            ->take(8)
            ->get();

        return view('products.show', compact('product', 'relatedProducts'));
    }
}
