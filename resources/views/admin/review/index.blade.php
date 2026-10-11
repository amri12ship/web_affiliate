@extends('admin.layouts.app')

@section('title', 'Review')

@section('content')
    <div class="mx-auto max-w-7xl">
        <div class="mb-6">
            <h1 class="text-xl font-semibold text-slate-800">Review Produk</h1>
            <p class="mt-1 text-sm text-slate-500">Pantau performa, rating, dan penjualan produk affiliate.</p>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="sa-card p-5">
                <p class="text-sm font-medium text-slate-500">Rata-rata Rating</p>
                <div class="mt-1 flex items-baseline gap-2">
                    <span class="text-3xl font-semibold text-slate-800">{{ number_format($averageRating, 2, ',', '.') }}</span>
                    <span class="text-sm text-slate-400">/ 5</span>
                </div>
                <p class="mt-0.5 text-xs text-slate-500">{{ number_format($ratedProducts) }} dari {{ number_format($totalProducts) }} produk dinilai</p>
            </div>

            <div class="sa-card p-5">
                <p class="text-sm font-medium text-slate-500">Rating Tertinggi</p>
                @if ($bestRated)
                    <p class="mt-1 truncate text-lg font-semibold text-slate-800" title="{{ $bestRated->name }}">{{ $bestRated->name }}</p>
                    <p class="mt-0.5 text-xs text-slate-500">{{ number_format((float) $bestRated->rating, 2, ',', '.') }} &middot; {{ $bestRated->category?->name ?? 'Tanpa kategori' }}</p>
                @else
                    <p class="mt-1 text-3xl font-semibold text-slate-800">-</p>
                    <p class="mt-0.5 text-xs text-slate-500">Belum ada rating</p>
                @endif
            </div>

            <div class="sa-card p-5">
                <p class="text-sm font-medium text-slate-500">Produk Terlaris</p>
                @if ($mostSold)
                    <p class="mt-1 truncate text-lg font-semibold text-slate-800" title="{{ $mostSold->name }}">{{ $mostSold->name }}</p>
                    <p class="mt-0.5 text-xs text-slate-500">{{ number_format($mostSold->sold_count) }} terjual</p>
                @else
                    <p class="mt-1 text-3xl font-semibold text-slate-800">-</p>
                    <p class="mt-0.5 text-xs text-slate-500">Belum ada penjualan</p>
                @endif
            </div>

            <div class="sa-card p-5">
                <p class="text-sm font-medium text-slate-500">Total Produk</p>
                <p class="mt-1 text-3xl font-semibold text-slate-800">{{ number_format($totalProducts) }}</p>
                <p class="mt-0.5 text-xs text-slate-500">Seluruh katalog</p>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-3">
            <!-- Rating Breakdown -->
            <div class="sa-card lg:col-span-1">
                <div class="border-b border-slate-100 px-5 py-4">
                    <h2 class="text-base font-semibold text-slate-800">Distribusi Rating</h2>
                </div>
                @php($maxRatingCount = max(1, (int) $ratingBreakdown->max('count')))
                <div class="space-y-3 px-5 py-5">
                    @foreach ($ratingBreakdown as $row)
                        <div class="flex items-center gap-3">
                            <span class="flex w-10 shrink-0 items-center gap-1 text-sm font-medium text-slate-600">
                                {{ $row['star'] }}
                                <svg class="h-3.5 w-3.5 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118L2.05 10.1c-.783-.57-.38-1.81.588-1.81h4.915a1 1 0 00.95-.69l1.519-4.674z" />
                                </svg>
                            </span>
                            <div class="h-2 flex-1 overflow-hidden rounded-full bg-slate-100">
                                <div
                                    class="h-full rounded-full bg-amber-400"
                                    style="width: {{ max(2, (int) round(($row['count'] / $maxRatingCount) * 100)) }}%"
                                ></div>
                            </div>
                            <span class="w-10 shrink-0 text-right text-sm text-slate-600">{{ number_format($row['count']) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Product List -->
            <div class="sa-card lg:col-span-2">
                <div class="border-b border-slate-100 p-4">
                    <form method="GET" action="{{ route('admin.reviews.index') }}" class="flex flex-col gap-3 sm:flex-row sm:items-center">
                        <input
                            type="text"
                            name="q"
                            value="{{ request('q') }}"
                            placeholder="Cari produk..."
                            class="w-full rounded-lg border border-slate-300 px-3.5 py-2 text-sm outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-200 sm:max-w-xs"
                        >
                        <select
                            name="category"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-sm outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-200 sm:w-auto"
                        >
                            <option value="">Semua Kategori</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected((string) request('category') === (string) $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        <select
                            name="sort"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-sm outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-200 sm:w-auto"
                        >
                            @foreach ($sortOptions as $value => $label)
                                <option value="{{ $value }}" @selected($sort === $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                        <button type="submit" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                            Terapkan
                        </button>
                    </form>
                </div>

                @if ($products->isEmpty())
                    <div class="flex flex-col items-center justify-center px-5 py-14 text-center">
                        <span class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118L2.05 10.1c-.783-.57-.38-1.81.588-1.81h4.915a1 1 0 00.95-.69l1.519-4.674z" />
                            </svg>
                        </span>
                        <p class="mt-3 text-sm font-medium text-slate-700">Tidak ada produk ditemukan</p>
                        <p class="mt-1 text-xs text-slate-500">Coba ubah kata kunci atau filter yang digunakan.</p>
                    </div>
                @else
                    <div class="divide-y divide-slate-100">
                        @foreach ($products as $product)
                            <div class="flex items-center gap-4 px-5 py-4 transition hover:bg-slate-50">
                                @if ($product->primaryImage)
                                    <img src="{{ $product->primaryImage->url() }}" alt="{{ $product->name }}" class="h-12 w-12 shrink-0 rounded-lg object-cover ring-1 ring-slate-200">
                                @else
                                    <span class="flex h-12 w-12 shrink-0 items-center justify-center rounded-lg bg-slate-100 text-sm font-semibold text-slate-500 ring-1 ring-slate-200">
                                        {{ strtoupper(substr($product->name, 0, 1)) }}
                                    </span>
                                @endif

                                <div class="min-w-0 flex-1">
                                    <p class="truncate font-medium text-slate-800" title="{{ $product->name }}">{{ $product->name }}</p>
                                    <div class="mt-0.5 flex flex-wrap items-center gap-x-2 gap-y-1 text-xs text-slate-500">
                                        <span>{{ $product->category?->name ?? 'Tanpa kategori' }}</span>
                                        <span aria-hidden="true">&middot;</span>
                                        <span class="inline-flex items-center gap-0.5">
                                            @for ($i = 1; $i <= 5; $i++)
                                                <svg class="h-3.5 w-3.5 {{ $i <= (int) round((float) $product->rating) ? 'text-amber-400' : 'text-slate-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118L2.05 10.1c-.783-.57-.38-1.81.588-1.81h4.915a1 1 0 00.95-.69l1.519-4.674z" />
                                                </svg>
                                            @endfor
                                        </span>
                                        {{ number_format((float) $product->rating, 1, ',', '.') }}
                                        <span aria-hidden="true">&middot;</span>
                                        <span>{{ number_format($product->sold_count) }} terjual</span>
                                        <span aria-hidden="true">&middot;</span>
                                        <span>{{ number_format($product->affiliate_clicks_count) }} klik</span>
                                    </div>
                                </div>

                                <a
                                    href="{{ route('admin.products.edit', $product) }}"
                                    class="shrink-0 rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-700 transition hover:bg-slate-50"
                                >
                                    Edit
                                </a>
                            </div>
                        @endforeach
                    </div>

                    @if ($products->hasPages())
                        <div class="border-t border-slate-100 px-5 py-4">
                            {{ $products->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
@endsection
