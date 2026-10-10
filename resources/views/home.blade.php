@extends('layouts.public')

@section('title', 'Beranda')

@section('content')
    <!-- Hero -->
    <section class="sa-hero">
        {{-- 3D ornaments placed to the right/edges. Heavier objects only appear
             from the "sm" breakpoint up so they never cover text on phones. --}}
        <div class="pointer-events-none absolute inset-0" aria-hidden="true">
            {{-- mobile-only, safely tucked into the corner --}}
            <x-sphere color="peach" size="h-32 w-32" class="bottom-[-1.5rem] right-[-1.5rem] opacity-30 float-slow sm:hidden" />

            <x-sphere color="peach" size="h-56 w-56" class="right-[-2.5rem] top-[-3rem] hidden opacity-80 float-slow sm:block sm:h-72 sm:w-72" />
            <x-sphere color="orange" size="h-24 w-24" class="right-[12%] top-[58%] hidden opacity-70 float-slower sm:block" />
            <x-sphere color="rose" size="h-16 w-16" class="bottom-[16%] right-[32%] hidden opacity-60 float-reverse md:block" />
            <x-ring size="h-40 w-40" class="right-[20%] top-[10%] hidden opacity-50 spin-slow sm:block" />
            <x-shape size="h-20 w-20" class="right-[7%] bottom-[12%] hidden opacity-70 float-reverse lg:block" />

            {{-- Floating "shopping" glass chips --}}
            <div class="sa-glass-dark sa-float-chip absolute right-[24%] top-[20%] hidden h-16 w-16 shadow-xl float-slow sm:flex">
                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.3 4.6A1 1 0 005.6 19H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
            </div>
            <div class="sa-glass-dark sa-float-chip absolute right-[6%] bottom-[26%] hidden h-14 w-14 shadow-xl float-slower md:flex">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
            </div>
        </div>

        <div class="relative mx-auto max-w-7xl px-4 py-16 sm:px-6 sm:py-24 lg:px-8">
            <div class="max-w-2xl">
                <span class="sa-glass-dark inline-flex items-center gap-2 rounded-full px-3.5 py-1.5 text-xs font-medium text-white">
                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-300"></span>
                    Rekomendasi terbaru setiap hari
                </span>
                <h1 class="sa-hero-title mt-5 text-3xl font-extrabold leading-tight tracking-tight sm:text-4xl lg:text-5xl">
                    Temukan Produk Menarik &amp; Viral di Shopee
                </h1>
                <p class="mt-4 max-w-xl text-base text-white/90 sm:text-lg">
                    Rekomendasi produk pilihan dengan harga menarik. Lengkap dengan tautan affiliate tepercaya.
                </p>
                <div class="mt-8 flex flex-wrap gap-3">
                    <a
                        href="{{ route('products.index') }}"
                        class="inline-flex items-center gap-2 rounded-full bg-white px-6 py-3 text-sm font-semibold text-orange-700 shadow-lg shadow-orange-900/20 transition hover:bg-orange-50 hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-orange-600"
                    >
                        Jelajahi Produk
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                        </svg>
                    </a>
                    <a
                        href="#trending"
                        class="sa-glass-dark inline-flex items-center gap-2 rounded-full px-6 py-3 text-sm font-semibold text-white transition hover:bg-white/25 focus:outline-none focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-orange-600"
                    >
                        Lihat Produk Trending
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Kategori -->
    <section id="kategori" class="mx-auto max-w-7xl scroll-mt-20 px-4 py-10 sm:px-6 lg:px-8">
        <div class="mb-5">
            <h2 class="text-lg font-bold text-slate-800 sm:text-xl">Kategori</h2>
            <p class="mt-0.5 text-sm text-slate-500">Telusuri produk berdasarkan kategori.</p>
        </div>

        @if ($categories->isEmpty())
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-5 py-10 text-center">
                <p class="text-sm text-slate-500">Belum ada kategori untuk saat ini.</p>
            </div>
        @else
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6">
                @foreach ($categories as $category)
                    <a
                        href="{{ route('products.index', ['category' => $category->id]) }}"
                        class="group flex flex-col items-center justify-center gap-2 rounded-2xl bg-white p-4 text-center shadow-sm ring-1 ring-black/5 transition hover:-translate-y-0.5 hover:shadow-md"
                    >
                        <span class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-50 text-base font-bold text-orange-600 transition group-hover:bg-orange-100">
                            {{ strtoupper(substr($category->name, 0, 1)) }}
                        </span>
                        <span class="line-clamp-2 text-xs font-medium text-slate-700 sm:text-sm">{{ $category->name }}</span>
                        <span class="text-[11px] text-slate-400">{{ $category->products_count }} produk</span>
                    </a>
                @endforeach
            </div>
        @endif
    </section>

    <!-- Trending -->
    <x-product-section
        id="trending"
        title="Produk Trending"
        subtitle="Produk yang sedang banyak dicari."
        :products="$trendingProducts"
        empty="Belum ada produk trending saat ini."
    />

    <!-- Viral -->
    <section class="bg-white">
        <x-product-section
            id="viral"
            title="Produk Viral"
            subtitle="Produk yang sedang ramai dibicarakan."
            :products="$viralProducts"
            empty="Belum ada produk viral untuk saat ini."
        />
    </section>

    <!-- Recommended -->
    <x-product-section
        id="produk-recommended"
        title="Produk Rekomendasi"
        subtitle="Pilihan terbaik untuk kamu."
        :products="$recommendedProducts"
        empty="Belum ada produk rekomendasi untuk saat ini."
    />

    <!-- Latest -->
    <section class="bg-white">
        <x-product-section
            id="produk-terbaru"
            title="Produk Terbaru"
            subtitle="Baru ditambahkan minggu ini."
            :products="$latestProducts"
            empty="Belum ada produk terbaru untuk saat ini."
        />
    </section>
@endsection
