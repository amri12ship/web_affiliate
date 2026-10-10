@extends('layouts.public')

@php
    $images = $product->images;
    $primaryImage = $images->first();
    $categoryName = optional($product->category)->name;
    $metaDescription = \Illuminate\Support\Str::limit(trim(strip_tags((string) $product->description)), 155);
    if ($metaDescription === '') {
        $metaDescription = 'Rekomendasi produk '.$product->name.' di Shopee dengan harga menarik.';
    }
@endphp

@section('title', $product->name)
@section('meta_description', $metaDescription)

@push('head')
    <meta property="og:type" content="product">
    <meta property="og:title" content="{{ $product->name }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta name="twitter:card" content="summary_large_image">
    @if ($primaryImage)
        <meta property="og:image" content="{{ $primaryImage->url() }}">
        <meta name="twitter:image" content="{{ $primaryImage->url() }}">
    @endif
@endpush

@section('content')
    <div class="relative mx-auto max-w-7xl px-4 py-6 sm:px-6 sm:py-8 lg:px-8">
        {{-- Ultra-soft ornament; product photos & data remain untouched. --}}
        <div class="pointer-events-none absolute inset-x-0 top-0 -z-10 h-80 overflow-hidden" aria-hidden="true">
            <x-sphere color="peach" size="h-44 w-44" class="right-[-2rem] top-[-3rem] opacity-30 float-slower" />
            <x-shape size="h-16 w-16" class="left-[4%] top-8 opacity-20 float-reverse" />
        </div>

        <nav class="mb-5 flex flex-wrap items-center gap-1.5 text-xs text-slate-500 sm:text-sm" aria-label="Breadcrumb">
            <a href="{{ route('home') }}" class="hover:text-orange-600">Home</a>
            <span class="text-slate-300">/</span>
            <a href="{{ route('products.index') }}" class="hover:text-orange-600">Produk</a>
            @if ($categoryName)
                <span class="text-slate-300">/</span>
                <a href="{{ route('products.index', ['category' => $product->category_id]) }}" class="hover:text-orange-600">{{ $categoryName }}</a>
            @endif
            <span class="text-slate-300">/</span>
            <span class="line-clamp-1 font-medium text-slate-700">{{ $product->name }}</span>
        </nav>

        <div class="grid grid-cols-1 gap-8 lg:grid-cols-2">
            {{-- Gallery --}}
            <div>
                <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-black/5">
                    @if ($primaryImage)
                        <img
                            id="product-main-image"
                            src="{{ $primaryImage->url() }}"
                            alt="{{ $product->name }}"
                            class="aspect-square w-full object-cover"
                        >
                    @else
                        <div class="flex aspect-square w-full items-center justify-center bg-slate-100 text-slate-300">
                            <svg class="h-16 w-16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                        </div>
                    @endif
                </div>

                @if ($images->count() > 1)
                    <div class="mt-3 grid grid-cols-3 gap-3">
                        @foreach ($images as $image)
                            <button
                                type="button"
                                data-gallery-thumb
                                data-src="{{ $image->url() }}"
                                aria-label="Tampilkan foto {{ $loop->iteration }}"
                                class="overflow-hidden rounded-xl bg-white ring-1 ring-slate-200 transition hover:ring-orange-300 focus:outline-none focus:ring-2 focus:ring-orange-500 {{ $loop->first ? 'ring-2 ring-orange-500' : '' }}"
                            >
                                <img src="{{ $image->url() }}" alt="{{ $product->name }} foto {{ $loop->iteration }}" class="aspect-square w-full object-cover">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Info --}}
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    @if ($categoryName)
                        <a href="{{ route('products.index', ['category' => $product->category_id]) }}" class="rounded-full bg-slate-100 px-3 py-1 text-xs font-medium text-slate-600 hover:bg-slate-200">
                            {{ $categoryName }}
                        </a>
                    @endif
                    @if ($product->is_trending)
                        <span class="rounded-full bg-orange-100 px-3 py-1 text-xs font-semibold text-orange-700">Trending</span>
                    @endif
                    @if ($product->is_viral)
                        <span class="rounded-full bg-rose-100 px-3 py-1 text-xs font-semibold text-rose-700">Viral</span>
                    @endif
                    @if ($product->is_recommended)
                        <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700">Recommended</span>
                    @endif
                </div>

                <h1 class="mt-3 text-xl font-bold leading-snug text-slate-900 sm:text-2xl lg:text-3xl">
                    {{ $product->name }}
                </h1>

                <div class="mt-3 flex flex-wrap items-center gap-3 text-sm text-slate-500">
                    <span class="inline-flex items-center gap-1">
                        <svg class="h-4 w-4 text-amber-400" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.958a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.367 2.446a1 1 0 00-.363 1.118l1.286 3.958c.3.921-.755 1.688-1.538 1.118l-3.367-2.446a1 1 0 00-1.176 0l-3.367 2.446c-.783.57-1.838-.197-1.538-1.118l1.286-3.958a1 1 0 00-.363-1.118L2.063 9.385c-.783-.57-.38-1.81.588-1.81h4.162a1 1 0 00.95-.69l1.286-3.958z" />
                        </svg>
                        <span class="font-semibold text-slate-700">{{ number_format((float) $product->rating, 1) }}</span>
                    </span>
                    <span class="h-1 w-1 rounded-full bg-slate-300"></span>
                    <span>{{ number_format($product->sold_count) }} terjual</span>
                </div>

                <p class="mt-5 text-3xl font-bold text-orange-600">Rp {{ number_format((float) $product->price, 0, ',', '.') }}</p>

                @if ($product->affiliate_url)
                    <a
                        href="{{ route('affiliate.go', ['product' => $product->slug]) }}"
                        target="_blank"
                        rel="noopener noreferrer nofollow sponsored"
                        class="mt-6 inline-flex w-full items-center justify-center gap-2 rounded-xl bg-orange-600 px-6 py-3.5 text-base font-semibold text-white transition hover:bg-orange-700 sm:w-auto"
                    >
                        <span aria-hidden="true">🛒</span> Beli di Shopee
                    </a>
                @endif

                @if ($product->description)
                    <div class="mt-8 border-t border-slate-200 pt-6">
                        <h2 class="text-base font-semibold text-slate-800">Deskripsi Produk</h2>
                        <div class="mt-3 text-sm leading-relaxed text-slate-600">
                            {!! nl2br(e($product->description)) !!}
                        </div>
                    </div>
                @endif

                <dl class="mt-6 grid grid-cols-2 gap-3 text-sm">
                    @if ($categoryName)
                        <div class="rounded-xl bg-white p-4 ring-1 ring-black/5">
                            <dt class="text-xs text-slate-400">Kategori</dt>
                            <dd class="mt-1 font-medium text-slate-700">{{ $categoryName }}</dd>
                        </div>
                    @endif
                    <div class="rounded-xl bg-white p-4 ring-1 ring-black/5">
                        <dt class="text-xs text-slate-400">Terjual</dt>
                        <dd class="mt-1 font-medium text-slate-700">{{ number_format($product->sold_count) }} produk</dd>
                    </div>
                </dl>
            </div>
        </div>

        @if ($relatedProducts->isNotEmpty())
            <section class="mt-12 border-t border-slate-200 pt-8">
                <h2 class="text-lg font-bold text-slate-800 sm:text-xl">Produk Terkait</h2>
                <p class="mt-0.5 text-sm text-slate-500">Produk lain dari kategori {{ $categoryName ?? 'yang sama' }}.</p>

                <div class="mt-5 grid grid-cols-2 gap-3 sm:gap-4 md:grid-cols-3 lg:grid-cols-4">
                    @foreach ($relatedProducts as $related)
                        <x-product-card :product="$related" />
                    @endforeach
                </div>
            </section>
        @endif
    </div>

    <script>
        (function () {
            var main = document.getElementById('product-main-image');
            var thumbs = document.querySelectorAll('[data-gallery-thumb]');

            if (!main || !thumbs.length) {
                return;
            }

            thumbs.forEach(function (thumb) {
                thumb.addEventListener('click', function () {
                    main.src = thumb.getAttribute('data-src');

                    thumbs.forEach(function (item) {
                        item.classList.remove('ring-2', 'ring-orange-500');
                        item.classList.add('ring-1', 'ring-slate-200');
                    });

                    thumb.classList.add('ring-2', 'ring-orange-500');
                    thumb.classList.remove('ring-1', 'ring-slate-200');
                });
            });
        })();
    </script>
@endsection
