@extends('admin.layouts.app')

@section('title', 'Edit Kategori')

@section('content')
    <div class="mx-auto max-w-4xl">
        <div class="mb-6">
            <a href="{{ route('admin.categories.index') }}" class="inline-flex items-center gap-1.5 text-sm font-medium text-slate-500 transition hover:text-slate-700">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali
            </a>
            <h1 class="mt-2 text-xl font-semibold text-slate-800">Edit Kategori</h1>
            <p class="mt-1 text-sm text-slate-500">Perbarui informasi kategori &ldquo;{{ $category->name }}&rdquo;.</p>
        </div>

        <div class="sa-card p-6 sm:p-8">
            @include('admin.categories.partials.form', ['category' => $category])
        </div>
    </div>
@endsection
