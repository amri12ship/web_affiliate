@extends('admin.layouts.app')

@section('title', 'Pengaturan')

@section('content')
    <div class="mx-auto max-w-3xl">
        <div class="mb-6">
            <h1 class="text-xl font-semibold text-slate-800">Pengaturan Situs</h1>
            <p class="mt-1 text-sm text-slate-500">Kelola informasi umum yang tampil di seluruh halaman.</p>
        </div>

        @if (session('status'))
            <div class="mb-6 rounded-lg bg-emerald-50 px-4 py-3 text-sm text-emerald-700 ring-1 ring-emerald-200">
                {{ session('status') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-6 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700 ring-1 ring-red-200" role="alert">
                <p class="font-medium">Periksa kembali data yang Anda isi.</p>
                <ul class="mt-1 list-inside list-disc space-y-0.5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="sa-card p-6 sm:p-8">
            <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6">
                @csrf
                @method('PUT')

                <div>
                    <label for="site_name" class="mb-1.5 block text-sm font-medium text-slate-700">Nama Situs <span class="text-red-500">*</span></label>
                    <input
                        id="site_name"
                        name="site_name"
                        type="text"
                        value="{{ old('site_name', $settings['site_name']) }}"
                        required
                        class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-200 @error('site_name') border-red-400 focus:border-red-500 focus:ring-red-200 @enderror"
                    >
                    <p class="mt-1 text-xs text-slate-500">Muncul pada judul halaman, navbar, dan footer.</p>
                    @error('site_name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="site_description" class="mb-1.5 block text-sm font-medium text-slate-700">Deskripsi Situs</label>
                    <textarea
                        id="site_description"
                        name="site_description"
                        rows="3"
                        class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-200 @error('site_description') border-red-400 focus:border-red-500 focus:ring-red-200 @enderror"
                    >{{ old('site_description', $settings['site_description']) }}</textarea>
                    @error('site_description')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label for="contact_email" class="mb-1.5 block text-sm font-medium text-slate-700">Email Kontak</label>
                        <input
                            id="contact_email"
                            name="contact_email"
                            type="email"
                            value="{{ old('contact_email', $settings['contact_email']) }}"
                            class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-200 @error('contact_email') border-red-400 focus:border-red-500 focus:ring-red-200 @enderror"
                        >
                        @error('contact_email')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="contact_phone" class="mb-1.5 block text-sm font-medium text-slate-700">Telepon Kontak</label>
                        <input
                            id="contact_phone"
                            name="contact_phone"
                            type="text"
                            value="{{ old('contact_phone', $settings['contact_phone']) }}"
                            class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-200 @error('contact_phone') border-red-400 focus:border-red-500 focus:ring-red-200 @enderror"
                        >
                        @error('contact_phone')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="footer_text" class="mb-1.5 block text-sm font-medium text-slate-700">Teks Footer</label>
                    <input
                        id="footer_text"
                        name="footer_text"
                        type="text"
                        value="{{ old('footer_text', $settings['footer_text']) }}"
                        class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-200 @error('footer_text') border-red-400 focus:border-red-500 focus:ring-red-200 @enderror"
                    >
                    <p class="mt-1 text-xs text-slate-500">Ditampilkan di bagian bawah halaman publik.</p>
                    @error('footer_text')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-end gap-3 border-t border-slate-100 pt-6">
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-orange-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-orange-700"
                    >
                        Simpan Pengaturan
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
