@props(['id', 'title', 'subtitle' => null, 'products', 'empty' => 'Belum ada produk untuk saat ini.'])

<section id="{{ $id }}" class="mx-auto max-w-7xl scroll-mt-20 px-4 py-8 sm:px-6 lg:px-8">
    <div class="mb-5 flex items-end justify-between gap-4">
        <div>
            <h2 class="text-lg font-bold text-slate-800 sm:text-xl">{{ $title }}</h2>
            @if ($subtitle)
                <p class="mt-0.5 text-sm text-slate-500">{{ $subtitle }}</p>
            @endif
        </div>
        <a href="{{ route('products.index') }}" class="hidden shrink-0 text-sm font-medium text-orange-600 hover:text-orange-700 sm:inline-flex">
            Lihat semua
        </a>
    </div>

    @if ($products->isEmpty())
        <div class="rounded-2xl border border-dashed border-slate-300 bg-white px-5 py-10 text-center">
            <p class="text-sm text-slate-500">{{ $empty }}</p>
        </div>
    @else
        <div class="grid grid-cols-2 gap-3 sm:gap-4 md:grid-cols-3 lg:grid-cols-4">
            @foreach ($products as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>
    @endif
</section>
