@props(['product', 'label' => 'Lihat Produk'])

@php
    $image = $product->relationLoaded('primaryImage')
        ? $product->primaryImage
        : $product->primaryImage()->first();
@endphp

<article data-testid="product-card" class="group flex flex-col overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-black/5 transition hover:shadow-md">
    <a href="{{ route('products.show', ['product' => $product->slug]) }}" class="block" aria-label="{{ $product->name }}">
        <div class="relative aspect-square w-full overflow-hidden bg-slate-100">
            @if ($image)
                <img src="{{ $image->url() }}" alt="{{ $product->name }}" class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
            @else
                <div class="flex h-full w-full items-center justify-center text-slate-300">
                    <svg class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
            @endif

            <div class="absolute inset-x-0 top-0 flex flex-wrap gap-1 p-2">
                @if ($product->is_trending)
                    <span class="rounded-full bg-orange-600 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-white">Trending</span>
                @endif
                @if ($product->is_viral)
                    <span class="rounded-full bg-rose-600 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-white">Viral</span>
                @endif
                @if ($product->is_recommended)
                    <span class="rounded-full bg-emerald-600 px-2 py-0.5 text-[10px] font-semibold uppercase tracking-wide text-white">Recommended</span>
                @endif
            </div>
        </div>
    </a>

    <div class="flex flex-1 flex-col p-3 sm:p-4">
        <h3 class="line-clamp-2 text-sm font-semibold text-slate-800">
            <a href="{{ route('products.show', ['product' => $product->slug]) }}" class="transition group-hover:text-orange-600">
                {{ $product->name }}
            </a>
        </h3>

        <p class="mt-2 text-base font-bold text-orange-600 sm:text-lg">Rp {{ number_format((float) $product->price, 0, ',', '.') }}</p>

        <div class="mt-2 flex flex-wrap items-center gap-x-2 gap-y-1 text-xs text-slate-500">
            <span class="inline-flex items-center gap-1">
                <svg class="h-3.5 w-3.5 text-amber-400" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.286 3.958a1 1 0 00.95.69h4.162c.969 0 1.371 1.24.588 1.81l-3.367 2.446a1 1 0 00-.363 1.118l1.286 3.958c.3.921-.755 1.688-1.538 1.118l-3.367-2.446a1 1 0 00-1.176 0l-3.367 2.446c-.783.57-1.838-.197-1.538-1.118l1.286-3.958a1 1 0 00-.363-1.118L2.063 9.385c-.783-.57-.38-1.81.588-1.81h4.162a1 1 0 00.95-.69l1.286-3.958z" />
                </svg>
                <span class="font-medium text-slate-700">{{ number_format((float) $product->rating, 1) }}</span>
            </span>
            <span class="text-slate-300">|</span>
            <span>{{ number_format($product->sold_count) }} terjual</span>
        </div>

        @if ($product->affiliate_url)
            <a
                href="{{ route('affiliate.go', ['product' => $product->slug]) }}"
                target="_blank"
                rel="noopener noreferrer nofollow sponsored"
                class="mt-4 inline-flex items-center justify-center gap-2 rounded-lg bg-orange-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-orange-700"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.3 4.6A1 1 0 005.6 19H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                {{ $label }}
            </a>
        @endif
    </div>
</article>
