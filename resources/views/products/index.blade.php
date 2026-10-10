@extends('layouts.public')

@section('title', 'Produk')

@php
    $sortLabels = [
        'newest' => 'Terbaru',
        'cheapest' => 'Harga termurah',
        'expensive' => 'Harga tertinggi',
        'rating' => 'Rating tertinggi',
        'popular' => 'Terpopuler',
    ];
@endphp

@section('content')
    <div class="relative mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
        {{-- Very soft ornament behind the page heading only. --}}
        <div class="pointer-events-none absolute inset-x-0 top-0 -z-10 h-72 overflow-hidden" aria-hidden="true">
            <x-sphere color="peach" size="h-40 w-40" class="right-[-2rem] top-[-3rem] opacity-40 float-slower" />
            <x-ring size="h-24 w-24" class="left-[6%] top-4 opacity-30 spin-slow" />
        </div>

        <div class="mb-6">
            <h1 class="text-xl font-bold text-slate-800 sm:text-2xl">Semua Produk</h1>
            <p class="mt-1 text-sm text-slate-500">Kumpulan produk pilihan dengan tautan affiliate Shopee.</p>
        </div>

        <form method="GET" action="{{ route('products.index') }}" class="sa-card mb-6 p-4">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div class="sm:col-span-2 lg:col-span-4">
                    <label for="search" class="mb-1 block text-xs font-medium text-slate-600">Cari produk</label>
                    <div class="relative">
                        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z" />
                            </svg>
                        </span>
                        <input
                            type="search"
                            id="search"
                            name="search"
                            value="{{ $filters['search'] }}"
                            placeholder="Cari nama atau deskripsi produk..."
                            class="w-full rounded-lg border border-slate-300 py-2.5 pl-9 pr-4 text-sm outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-200"
                        >
                    </div>
                </div>

                <div>
                    <label for="category" class="mb-1 block text-xs font-medium text-slate-600">Kategori</label>
                    <select
                        id="category"
                        name="category"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200"
                    >
                        <option value="">Semua Kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected($filters['category'] === $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="min_price" class="mb-1 block text-xs font-medium text-slate-600">Harga minimum</label>
                    <input
                        type="number"
                        id="min_price"
                        name="min_price"
                        min="0"
                        step="1000"
                        value="{{ $filters['min_price'] !== null ? (int) $filters['min_price'] : '' }}"
                        placeholder="0"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200"
                    >
                </div>

                <div>
                    <label for="max_price" class="mb-1 block text-xs font-medium text-slate-600">Harga maksimum</label>
                    <input
                        type="number"
                        id="max_price"
                        name="max_price"
                        min="0"
                        step="1000"
                        value="{{ $filters['max_price'] !== null ? (int) $filters['max_price'] : '' }}"
                        placeholder="Tanpa batas"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200"
                    >
                </div>

                <div>
                    <label for="min_rating" class="mb-1 block text-xs font-medium text-slate-600">Rating minimum</label>
                    <select
                        id="min_rating"
                        name="min_rating"
                        class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200"
                    >
                        <option value="">Semua Rating</option>
                        @foreach ([4, 3, 2, 1] as $rating)
                            <option value="{{ $rating }}" @selected($filters['min_rating'] !== null && (int) $filters['min_rating'] === $rating)>{{ $rating }}.0 ke atas</option>
                        @endforeach
                    </select>
                </div>

                <div class="sm:col-span-2 lg:col-span-4">
                    <label for="sort" class="mb-1 block text-xs font-medium text-slate-600">Urutkan</label>
                    <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                        <select
                            id="sort"
                            name="sort"
                            class="w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-sm outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200 sm:max-w-xs"
                        >
                            @foreach ($sortLabels as $value => $label)
                                <option value="{{ $value }}" @selected($filters['sort'] === $value)>{{ $label }}</option>
                            @endforeach
                        </select>

                        <div class="flex items-center gap-2">
                            <button type="submit" class="inline-flex items-center justify-center gap-2 rounded-lg bg-orange-600 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-orange-700">
                                Terapkan
                            </button>
                            <a href="{{ route('products.index') }}" class="inline-flex items-center justify-center rounded-lg border border-slate-300 px-5 py-2.5 text-sm font-medium text-slate-600 transition hover:bg-slate-50">
                                Reset
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        @if ($products->isEmpty())
            <div class="sa-card flex flex-col items-center justify-center px-5 py-16 text-center">
                <span class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z" />
                    </svg>
                </span>
                <p class="mt-3 text-sm font-medium text-slate-700">Produk yang kamu cari belum ditemukan.</p>
                <a href="{{ route('products.index') }}" class="mt-5 inline-flex items-center justify-center gap-2 rounded-lg bg-orange-600 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-orange-700">
                    Lihat Semua Produk
                </a>
            </div>
        @else
            <div class="grid grid-cols-2 gap-3 sm:gap-4 md:grid-cols-3 lg:grid-cols-4">
                @foreach ($products as $product)
                    <x-product-card :product="$product" label="Beli di Shopee" />
                @endforeach
            </div>

            @if ($products->hasPages())
                <div class="mt-8">
                    {{ $products->links() }}
                </div>
            @endif
        @endif
    </div>
@endsection
