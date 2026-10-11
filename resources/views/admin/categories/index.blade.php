@extends('admin.layouts.app')

@section('title', 'Kategori')

@section('content')
    <div class="mx-auto max-w-7xl">
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-xl font-semibold text-slate-800">Kategori</h1>
                <p class="mt-1 text-sm text-slate-500">Kelola kategori produk affiliate.</p>
            </div>
            <a
                href="{{ route('admin.categories.create') }}"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-orange-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-orange-700"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Kategori
            </a>
        </div>

        @if (session('status'))
            <div class="mb-6 rounded-lg bg-emerald-50 px-4 py-3 text-sm text-emerald-700 ring-1 ring-emerald-200">
                {{ session('status') }}
            </div>
        @endif

        <div class="sa-card">
            <div class="border-b border-slate-100 p-4">
                <form method="GET" action="{{ route('admin.categories.index') }}" class="flex max-w-sm items-center gap-2">
                    <input
                        type="text"
                        name="q"
                        value="{{ request('q') }}"
                        placeholder="Cari kategori..."
                        class="w-full rounded-lg border border-slate-300 px-3.5 py-2 text-sm outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-200"
                    >
                    <button type="submit" class="rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50">
                        Cari
                    </button>
                </form>
            </div>

            @if ($categories->isEmpty())
                <div class="flex flex-col items-center justify-center px-5 py-14 text-center">
                    <span class="flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7" />
                        </svg>
                    </span>
                    <p class="mt-3 text-sm font-medium text-slate-700">Belum ada kategori</p>
                    <p class="mt-1 text-xs text-slate-500">Mulai dengan menambahkan kategori pertama.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-100 text-left text-sm">
                        <thead class="bg-slate-50 text-xs uppercase tracking-wide text-slate-500">
                            <tr>
                                <th class="px-5 py-3 font-semibold">Gambar</th>
                                <th class="px-5 py-3 font-semibold">Nama</th>
                                <th class="px-5 py-3 font-semibold">Slug</th>
                                <th class="px-5 py-3 font-semibold">Produk</th>
                                <th class="px-5 py-3 font-semibold">Status</th>
                                <th class="px-5 py-3 text-right font-semibold">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($categories as $category)
                                <tr class="transition hover:bg-slate-50">
                                    <td class="px-5 py-3">
                                        @if ($category->image)
                                            <img src="{{ $category->imageUrl() }}" alt="{{ $category->name }}" class="h-11 w-11 rounded-lg object-cover ring-1 ring-slate-200">
                                        @else
                                            <span class="flex h-11 w-11 items-center justify-center rounded-lg bg-slate-100 text-sm font-semibold text-slate-500 ring-1 ring-slate-200">
                                                {{ strtoupper(substr($category->name, 0, 1)) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3">
                                        <p class="font-medium text-slate-800">{{ $category->name }}</p>
                                        @if ($category->description)
                                            <p class="mt-0.5 line-clamp-1 max-w-xs text-xs text-slate-500">{{ $category->description }}</p>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3">
                                        <code class="rounded bg-slate-100 px-2 py-0.5 text-xs text-slate-600">{{ $category->slug }}</code>
                                    </td>
                                    <td class="px-5 py-3 text-slate-600">{{ $category->products_count }}</td>
                                    <td class="px-5 py-3">
                                        @if ($category->isActive())
                                            <span class="inline-flex rounded-full bg-emerald-50 px-2.5 py-0.5 text-xs font-semibold text-emerald-700">Active</span>
                                        @else
                                            <span class="inline-flex rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-600">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-3">
                                        <div class="flex items-center justify-end gap-2">
                                            <a
                                                href="{{ route('admin.categories.edit', $category) }}"
                                                class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-700 transition hover:bg-slate-50"
                                            >
                                                Edit
                                            </a>

                                            <form method="POST" action="{{ route('admin.categories.toggle', $category) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button
                                                    type="submit"
                                                    class="rounded-lg border border-slate-300 px-3 py-1.5 text-xs font-medium text-slate-700 transition hover:bg-slate-50"
                                                >
                                                    {{ $category->isActive() ? 'Nonaktifkan' : 'Aktifkan' }}
                                                </button>
                                            </form>

                                            <form
                                                method="POST"
                                                action="{{ route('admin.categories.destroy', $category) }}"
                                                onsubmit="return confirm('Yakin ingin menghapus kategori ini? Tindakan ini tidak dapat dibatalkan.');"
                                            >
                                                @csrf
                                                @method('DELETE')
                                                <button
                                                    type="submit"
                                                    class="rounded-lg border border-red-200 px-3 py-1.5 text-xs font-medium text-red-600 transition hover:bg-red-50"
                                                >
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if ($categories->hasPages())
                    <div class="border-t border-slate-100 px-5 py-4">
                        {{ $categories->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
@endsection
