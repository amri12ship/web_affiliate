@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="mx-auto max-w-7xl">
        <div class="mb-6">
            <h1 class="text-xl font-semibold text-slate-800">Dashboard</h1>
            <p class="mt-1 text-sm text-slate-500">Ringkasan data Shopee Affiliate.</p>
        </div>

        <!-- Statistic Cards -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="sa-card p-5">
                <div class="flex items-center justify-between">
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-orange-100 text-orange-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </span>
                </div>
                <p class="mt-4 text-sm font-medium text-slate-500">Total Produk</p>
                <p class="mt-1 text-3xl font-semibold text-slate-800">{{ number_format($totalProducts) }}</p>
            </div>

            <div class="sa-card p-5">
                <div class="flex items-center justify-between">
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-100 text-blue-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7" />
                        </svg>
                    </span>
                </div>
                <p class="mt-4 text-sm font-medium text-slate-500">Total Kategori</p>
                <p class="mt-1 text-3xl font-semibold text-slate-800">{{ number_format($totalCategories) }}</p>
            </div>

            <div class="sa-card p-5">
                <div class="flex items-center justify-between">
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.525m16.862 0l-2.899.603M6.343 17.657L4.93 19.07m7.07-2.556l-2.83 2.829" />
                        </svg>
                    </span>
                </div>
                <p class="mt-4 text-sm font-medium text-slate-500">Total Klik Affiliate</p>
                <p class="mt-1 text-3xl font-semibold text-slate-800">{{ number_format($totalClicks) }}</p>
            </div>

            <div class="sa-card p-5">
                <div class="flex items-center justify-between">
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-rose-100 text-rose-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </span>
                </div>
                <p class="mt-4 text-sm font-medium text-slate-500">Produk Trending</p>
                @if ($trendingProduct)
                    <p class="mt-1 truncate text-lg font-semibold text-slate-800" title="{{ $trendingProduct->name }}">{{ $trendingProduct->name }}</p>
                    <p class="mt-0.5 text-xs text-slate-500">{{ number_format($trendingProduct->affiliate_clicks_count) }} klik</p>
                @else
                    <p class="mt-1 text-3xl font-semibold text-slate-800">0</p>
                    <p class="mt-0.5 text-xs text-slate-500">Belum ada data</p>
                @endif
            </div>
        </div>

        <!-- Statistik Klik -->
        <div class="mt-6">
            <h2 class="mb-3 text-base font-semibold text-slate-800">Statistik Klik</h2>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="sa-card p-5">
                    <p class="text-sm font-medium text-slate-500">Klik Hari Ini</p>
                    <p class="mt-1 text-3xl font-semibold text-slate-800">{{ number_format($clicksToday) }}</p>
                </div>
                <div class="sa-card p-5">
                    <p class="text-sm font-medium text-slate-500">Klik Minggu Ini</p>
                    <p class="mt-1 text-3xl font-semibold text-slate-800">{{ number_format($clicksThisWeek) }}</p>
                </div>
                <div class="sa-card p-5">
                    <p class="text-sm font-medium text-slate-500">Klik Bulan Ini</p>
                    <p class="mt-1 text-3xl font-semibold text-slate-800">{{ number_format($clicksThisMonth) }}</p>
                </div>
            </div>
        </div>

        <!-- Produk Paling Banyak Diklik -->
        <div class="mt-6 sa-card">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                <div>
                    <h2 class="text-base font-semibold text-slate-800">Produk Paling Banyak Diklik</h2>
                    <p class="mt-0.5 text-sm text-slate-500">Diurutkan dari klik terbanyak.</p>
                </div>
            </div>

            @if ($topProducts->isEmpty())
                <div class="px-5 py-10 text-center">
                    <p class="text-sm text-slate-500">Belum ada klik affiliate.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 text-left text-sm">
                        <thead>
                            <tr class="text-xs font-semibold uppercase tracking-wide text-slate-400">
                                <th class="px-5 py-3">Produk</th>
                                <th class="px-5 py-3">Kategori</th>
                                <th class="px-5 py-3 text-right">Total Klik</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($topProducts as $product)
                                <tr class="transition hover:bg-slate-50">
                                    <td class="px-5 py-3">
                                        <p class="font-medium text-slate-800">{{ $product->name }}</p>
                                    </td>
                                    <td class="px-5 py-3 text-slate-600">
                                        {{ $product->category?->name ?? '-' }}
                                    </td>
                                    <td class="px-5 py-3 text-right font-semibold text-slate-800">
                                        {{ number_format($product->affiliate_clicks_count) }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- Recent Products -->
        <div class="mt-6 sa-card">
            <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                <div>
                    <h2 class="text-base font-semibold text-slate-800">Recent Products</h2>
                    <p class="text-xs text-slate-500">Produk terbaru yang ditambahkan.</p>
                </div>
            </div>

            @if ($recentProducts->isEmpty())
                <div class="flex flex-col items-center justify-center px-5 py-14 text-center">
                    <span class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </span>
                    <p class="mt-3 text-sm font-medium text-slate-700">Belum ada produk</p>
                    <p class="mt-1 text-xs text-slate-500">Data produk akan tampil di sini setelah ditambahkan.</p>
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
                                <th class="px-5 py-3 font-semibold">Status</th>
                                <th class="px-5 py-3 font-semibold">Tanggal Dibuat</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($recentProducts as $product)
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
                                    </td>
                                    <td class="px-5 py-3 text-slate-600">
                                        {{ $product->category?->name ?? '-' }}
                                    </td>
                                    <td class="px-5 py-3 font-medium text-slate-700">
                                        Rp {{ number_format((float) $product->price, 0, ',', '.') }}
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
                                    <td class="px-5 py-3 text-slate-600">
                                        {{ $product->created_at?->format('d M Y') ?? '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection
