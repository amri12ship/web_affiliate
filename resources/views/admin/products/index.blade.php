@extends('admin.layouts.app')

@section('title', 'Produk')

@section('content')
    <div class="mx-auto max-w-7xl">
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-semibold text-slate-800">Produk</h1>
                <p class="mt-1 text-sm text-slate-500">Kelola produk Shopee Affiliate.</p>
            </div>
            <a
                href="{{ route('admin.products.create') }}"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-orange-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-orange-700"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Produk
            </a>
        </div>

        @if (session('status'))
            <div class="mb-6 rounded-lg bg-emerald-50 px-4 py-3 text-sm text-emerald-700 ring-1 ring-emerald-200">
                {{ session('status') }}
            </div>
        @endif

        <div class="sa-card">
            <div class="border-b border-slate-100 p-4">
                <form method="GET" action="{{ route('admin.products.index') }}" class="flex flex-wrap items-center gap-2">
                    <input
                        type="text"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Cari produk..."
                        class="w-full max-w-xs rounded-lg border border-slate-300 px-3.5 py-2 text-sm outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-200"
                    >
                    <select name="category" class="rounded-lg border border-slate-300 bg-white px-3.5 py-2 text-sm outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200">
                        <option value="">Semua Kategori</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected((int) request('category') === $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">Filter</button>
                </form>
            </div>

            @if ($products->isEmpty())
                <div class="flex flex-col items-center justify-center px-5 py-14 text-center">
                    <span class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </span>
                    <p class="mt-3 text-sm font-medium text-slate-700">Belum ada produk</p>
                    <p class="mt-1 text-xs text-slate-500">Mulai dengan menambahkan produk pertama.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-5 py-3 font-semibold">Foto</th>
                                <th class="px-5 py-3 font-semibold">Nama Produk</th>
                                <th class="px-5 py-3 font-semibold">Kategori</th>
                                <th class="px-5 py-3 font-semibold">Harga</th>
                                <th class="px-5 py-3 font-semibold">Rating</th>
                                <th class="px-5 py-3 font-semibold">Terjual</th>
                                <th class="px-5 py-3 font-semibold">Label</th>
                                <th class="px-5 py-3 font-semibold">Status</th>
                                <th class="px-5 py-3 text-right font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($products as $product)
                                <tr class="transition hover:bg-slate-50">
                                    <td class="px-5 py-3">
                                        @if ($product->primaryImage)
                                            <img
                                                src="{{ $product->primaryImage->url() }}"
                                                alt="{{ $product->name }}"
                                                class="h-11 w-11 rounded-lg object-cover ring-1 ring-slate-200"
                                            >
                                        @else
                                            <span class="flex h-11 w-11 items-center justify-center rounded-lg bg-slate-100 text-sm font-semibold text-slate-500 ring-1 ring-slate-200">
                                                {{ strtoupper(substr($product->name, 0, 1)) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3">
                                        <p class="font-medium text-slate-800">{{ $product->name }}</p>
                                        <p class="text-xs text-slate-400">{{ $product->slug }}</p>
                                    </td>
                                    <td class="px-5 py-3 text-slate-600">{{ $product->category?->name ?? '-' }}</td>
                                    <td class="px-5 py-3 font-medium text-slate-700">Rp {{ number_format((float) $product->price, 0, ',', '.') }}</td>
                                    <td class="px-5 py-3 text-slate-600">{{ number_format((float) $product->rating, 1) }}</td>
                                    <td class="px-5 py-3 text-slate-600">{{ number_format($product->sold_count) }}</td>
                                    <td class="px-5 py-3">
                                        <div class="flex flex-wrap gap-1">
                                            @if ($product->is_trending)
                                                <span class="rounded-full bg-rose-50 px-2 py-0.5 text-[10px] font-semibold uppercase text-rose-600">Trending</span>
                                            @endif
                                            @if ($product->is_viral)
                                                <span class="rounded-full bg-purple-50 px-2 py-0.5 text-[10px] font-semibold uppercase text-purple-600">Viral</span>
                                            @endif
                                            @if ($product->is_recommended)
                                                <span class="rounded-full bg-blue-50 px-2 py-0.5 text-[10px] font-semibold uppercase text-blue-600">Recommended</span>
                                            @endif
                                            @unless ($product->is_trending || $product->is_viral || $product->is_recommended)
                                                <span class="text-xs text-slate-400">-</span>
                                            @endunless
                                        </div>
                                    </td>
                                    <td class="px-5 py-3">
                                        @php
                                            $statusClasses = [
                                                'active' => 'bg-emerald-50 text-emerald-700',
                                                'inactive' => 'bg-slate-100 text-slate-600',
                                                'draft' => 'bg-amber-50 text-amber-700',
                                            ];
                                        @endphp
                                        <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold capitalize {{ $statusClasses[$product->status] ?? 'bg-slate-100 text-slate-600' }}">
                                            {{ $product->status }}
                                        </span>
                                    </td>
                                    <td class="px-5 py-3">
                                        <div class="flex items-center justify-end gap-2">
                                            <a
                                                href="{{ route('admin.products.edit', $product) }}"
                                                class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-700 transition hover:bg-slate-50"
                                            >
                                                Edit
                                            </a>
                                            <form
                                                method="POST"
                                                action="{{ route('admin.products.destroy', $product) }}"
                                                onsubmit="return confirm('Yakin ingin menghapus produk ini? Semua foto produk juga akan dihapus.');"
                                            >
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="rounded-lg border border-red-200 px-3 py-1.5 text-xs font-medium text-red-600 transition hover:bg-red-50">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($products->hasPages())
                    <div class="border-t border-slate-100 px-5 py-4">
                        {{ $products->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
@endsection
