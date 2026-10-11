@extends('admin.layouts.app')

@section('title', 'Statistik')

@section('content')
    <div class="mx-auto max-w-7xl">
        <div class="mb-6">
            <h1 class="text-xl font-semibold text-slate-800">Statistik</h1>
            <p class="mt-1 text-sm text-slate-500">Analitik klik affiliate dan performa produk.</p>
        </div>

        <!-- Statistic Cards -->
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <div class="sa-card p-5">
                <div class="flex items-center justify-between">
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-orange-100 text-orange-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.525m16.862 0l-2.899.603M6.343 17.657L4.93 19.07m7.07-2.556l-2.83 2.829" />
                        </svg>
                    </span>
                </div>
                <p class="mt-4 text-sm font-medium text-slate-500">Total Klik</p>
                <p class="mt-1 text-3xl font-semibold text-slate-800">{{ number_format($totalClicks) }}</p>
            </div>

            <div class="sa-card p-5">
                <div class="flex items-center justify-between">
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-blue-100 text-blue-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </span>
                </div>
                <p class="mt-4 text-sm font-medium text-slate-500">Klik Hari Ini</p>
                <p class="mt-1 text-3xl font-semibold text-slate-800">{{ number_format($clicksToday) }}</p>
                <p class="mt-0.5 text-xs text-slate-500">{{ number_format($clicksThisWeek) }} minggu ini</p>
            </div>

            <div class="sa-card p-5">
                <div class="flex items-center justify-between">
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </span>
                </div>
                <p class="mt-4 text-sm font-medium text-slate-500">Klik Bulan Ini</p>
                <p class="mt-1 text-3xl font-semibold text-slate-800">{{ number_format($clicksThisMonth) }}</p>
            </div>

            <div class="sa-card p-5">
                <div class="flex items-center justify-between">
                    <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-rose-100 text-rose-600">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                        </svg>
                    </span>
                </div>
                <p class="mt-4 text-sm font-medium text-slate-500">Rata-rata Klik/Produk</p>
                <p class="mt-1 text-3xl font-semibold text-slate-800">{{ number_format($averageClicks, 1, ',', '.') }}</p>
                <p class="mt-0.5 text-xs text-slate-500">{{ number_format($productsWithClicks) }} produk diklik</p>
            </div>
        </div>

        <!-- Grafik Klik 30 Hari -->
        <div class="mt-6 sa-card">
            <div class="border-b border-slate-100 px-5 py-4">
                <h2 class="text-base font-semibold text-slate-800">Klik 30 Hari Terakhir</h2>
                <p class="mt-0.5 text-sm text-slate-500">Jumlah klik affiliate per hari.</p>
            </div>
            <div class="px-5 py-6">
                <div class="flex h-56 items-end gap-1.5">
                    @foreach ($dailyClicks as $day)
                        <div class="group relative flex h-full flex-1 flex-col items-center justify-end">
                            <span class="pointer-events-none absolute -top-7 hidden whitespace-nowrap rounded-md bg-slate-800 px-2 py-1 text-xs font-medium text-white group-hover:block">
                                {{ number_format($day['total']) }} &middot; {{ $day['label'] }}
                            </span>
                            <div
                                class="w-full rounded-t bg-gradient-to-t from-orange-500 to-rose-400 transition group-hover:from-orange-600 group-hover:to-rose-500"
                                style="height: {{ max(2, (int) round(($day['total'] / $maxDailyClicks) * 100)) }}%"
                            ></div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-3 flex justify-between text-xs text-slate-400">
                    <span>{{ $dailyClicks->first()['label'] }}</span>
                    <span>{{ $dailyClicks->last()['label'] }}</span>
                </div>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
            <!-- Top Products -->
            <div class="sa-card">
                <div class="border-b border-slate-100 px-5 py-4">
                    <h2 class="text-base font-semibold text-slate-800">Produk Paling Banyak Diklik</h2>
                    <p class="mt-0.5 text-sm text-slate-500">10 produk teratas berdasarkan klik.</p>
                </div>

                @if ($topProducts->isEmpty())
                    <div class="px-5 py-10 text-center">
                        <p class="text-sm text-slate-500">Belum ada data klik affiliate.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-100 text-left text-sm">
                            <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                                <tr>
                                    <th class="px-5 py-3 font-semibold">Produk</th>
                                    <th class="px-5 py-3 text-right font-semibold">Klik</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach ($topProducts as $product)
                                    <tr class="transition hover:bg-slate-50">
                                        <td class="px-5 py-3">
                                            <p class="font-medium text-slate-800">{{ $product->name }}</p>
                                            <p class="mt-0.5 text-xs text-slate-500">{{ $product->category?->name ?? 'Tanpa kategori' }}</p>
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

            <!-- Category Breakdown -->
            <div class="sa-card">
                <div class="border-b border-slate-100 px-5 py-4">
                    <h2 class="text-base font-semibold text-slate-800">Klik per Kategori</h2>
                    <p class="mt-0.5 text-sm text-slate-500">Distribusi klik berdasarkan kategori produk.</p>
                </div>

                @if ($categoryStats->isEmpty())
                    <div class="px-5 py-10 text-center">
                        <p class="text-sm text-slate-500">Belum ada data klik per kategori.</p>
                    </div>
                @else
                    @php($maxCategoryClicks = max(1, (int) $categoryStats->max('clicks')))
                    <div class="space-y-4 px-5 py-5">
                        @foreach ($categoryStats as $category)
                            <div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="font-medium text-slate-700">{{ $category->name }}</span>
                                    <span class="font-semibold text-slate-800">{{ number_format($category->clicks) }}</span>
                                </div>
                                <div class="mt-1.5 h-2 w-full overflow-hidden rounded-full bg-slate-100">
                                    <div
                                        class="h-full rounded-full bg-gradient-to-r from-orange-400 to-rose-500"
                                        style="width: {{ max(2, (int) round(($category->clicks / $maxCategoryClicks) * 100)) }}%"
                                    ></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
