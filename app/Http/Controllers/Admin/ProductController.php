<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProductController extends Controller
{
    /**
     * Display a listing of the products.
     */
    public function index(Request $request): View
    {
        $products = Product::query()
            ->with(['category', 'primaryImage'])
            ->when($request->filled('q'), function ($query) use ($request): void {
                $query->where('name', 'like', '%'.$request->string('q').'%');
            })
            ->when($request->filled('category'), function ($query) use ($request): void {
                $query->where('category_id', $request->integer('category'));
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $categories = Category::orderBy('name')->get();

        return view('admin.products.index', compact('products', 'categories'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create(): View
    {
        $categories = Category::orderBy('name')->get();

        return view('admin.products.create', [
            'product' => new Product(['status' => Product::STATUS_ACTIVE, 'rating' => 0, 'sold_count' => 0]),
            'categories' => $categories,
        ]);
    }

    /**
     * Store a newly created product.
     */
    public function store(ProductRequest $request): RedirectResponse
    {
        $data = $request->validated();
        unset($data['images'], $data['delete_images']);

        $product = Product::create($data);

        $this->attachImages($product, $request->file('images', []));
        $this->resequenceImages($product);

        return redirect()
            ->route('admin.products.index')
            ->with('status', 'Produk berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product): View
    {
        $product->load('images');
        $categories = Category::orderBy('name')->get();

        return view('admin.products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified product.
     */
    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        $data = $request->validated();
        unset($data['images'], $data['delete_images']);

        $product->update($data);

        $deleteIds = array_filter((array) $request->input('delete_images', []));

        if ($deleteIds !== []) {
            $images = $product->images()->whereIn('id', $deleteIds)->get();

            foreach ($images as $image) {
                Storage::disk('public')->delete($image->image_path);
                $image->delete();
            }
        }

        $this->attachImages($product, $request->file('images', []));
        $this->resequenceImages($product);

        return redirect()
            ->route('admin.products.index')
            ->with('status', 'Produk berhasil diperbarui.');
    }

    /**
     * Remove the specified product.
     */
    public function destroy(Product $product): RedirectResponse
    {
        foreach ($product->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }

        $product->delete();

        return redirect()
            ->route('admin.products.index')
            ->with('status', 'Produk berhasil dihapus.');
    }

    /**
     * Store the uploaded images for a product.
     *
     * @param  array<int, UploadedFile>  $files
     */
    protected function attachImages(Product $product, array $files): void
    {
        $order = Product::MAX_IMAGES;

        foreach ($files as $file) {
            if (! $file instanceof UploadedFile) {
                continue;
            }

            $path = $file->store('products', 'public');

            $product->images()->create([
                'image_path' => $path,
                'sort_order' => ++$order,
            ]);
        }
    }

    /**
     * Resequence the product images so the sort order starts at 1.
     */
    protected function resequenceImages(Product $product): void
    {
        $images = $product->images()->orderBy('sort_order')->orderBy('id')->get();

        $order = 1;

        foreach ($images as $image) {
            if ($image->sort_order !== $order) {
                $image->update(['sort_order' => $order]);
            }

            $order++;
        }
    }
}
