<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Beranda') &middot; {{ $siteName }}</title>
    @hasSection('meta_description')
        <meta name="description" content="@yield('meta_description')">
    @endif
    <link rel="canonical" href="@yield('canonical', url()->current())">
    @stack('head')
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="sa-page min-h-screen text-slate-800 antialiased">
    {{-- Shared, extremely subtle 3D decorations behind every public page. --}}
    <div class="sa-decor" aria-hidden="true">
        <x-sphere color="peach" size="h-40 w-40" class="left-[-4rem] top-28 opacity-40 float-slower" />
        <x-sphere color="orange" size="h-20 w-20" class="right-[5%] top-[42%] opacity-30 float-slow" />
        <x-ring size="h-24 w-24" class="right-[8%] bottom-28 opacity-30 spin-slow" />
        <x-shape size="h-16 w-16" class="left-[6%] bottom-40 opacity-25 float-reverse" />
    </div>

    @php
        $home = route('home');
        $navLinks = [
            ['label' => 'Home', 'href' => $home, 'active' => request()->routeIs('home')],
            ['label' => 'Kategori', 'href' => $home.'#kategori', 'active' => false],
            ['label' => 'Trending', 'href' => $home.'#trending', 'active' => false],
            ['label' => 'Promo', 'href' => $home.'#viral', 'active' => false],
            ['label' => 'Review', 'href' => route('products.index'), 'active' => request()->routeIs('products.index')],
        ];
    @endphp

    <!-- Navbar -->
    <header class="sticky top-0 z-40 border-b border-slate-200 bg-white/90 backdrop-blur">
        <nav class="mx-auto flex h-16 max-w-7xl items-center gap-4 px-4 sm:px-6 lg:px-8" aria-label="Navigasi utama">
            <a href="{{ $home }}" class="flex shrink-0 items-center gap-2.5">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-orange-400 via-orange-500 to-rose-500 text-sm font-bold text-white shadow-md shadow-orange-500/30">SA</span>
                <span class="text-sm font-semibold text-slate-800 sm:text-base">{{ $siteName }}</span>
            </a>

            <ul class="ml-4 hidden items-center gap-1 lg:flex">
                @foreach ($navLinks as $link)
                    <li>
                        <a
                            href="{{ $link['href'] }}"
                            class="rounded-lg px-3 py-2 text-sm font-medium transition {{ $link['active'] ? 'text-orange-600' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}"
                        >
                            {{ $link['label'] }}
                        </a>
                    </li>
                @endforeach
            </ul>

            <form action="{{ route('products.index') }}" method="GET" class="ml-auto hidden max-w-xs flex-1 items-center md:flex">
                <div class="relative w-full">
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z" />
                        </svg>
                    </span>
                    <input
                        type="search"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari produk..."
                        class="w-full rounded-full border border-slate-300 py-2 pl-9 pr-4 text-sm outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-200"
                    >
                </div>
            </form>

            <button
                type="button"
                id="public-nav-toggle"
                class="ml-auto inline-flex h-10 w-10 items-center justify-center rounded-lg text-slate-600 hover:bg-slate-100 lg:hidden"
                aria-label="Buka menu"
                aria-expanded="false"
            >
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                </svg>
            </button>
        </nav>

        <div id="public-nav-menu" class="hidden border-t border-slate-200 bg-white px-4 py-4 lg:hidden">
            <form action="{{ route('products.index') }}" method="GET" class="mb-3 md:hidden">
                <div class="relative">
                    <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M17 11a6 6 0 11-12 0 6 6 0 0112 0z" />
                        </svg>
                    </span>
                    <input
                        type="search"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Cari produk..."
                        class="w-full rounded-full border border-slate-300 py-2 pl-9 pr-4 text-sm outline-none focus:border-orange-500 focus:ring-2 focus:ring-orange-200"
                    >
                </div>
            </form>

            <ul class="space-y-1">
                @foreach ($navLinks as $link)
                    <li>
                        <a
                            href="{{ $link['href'] }}"
                            class="block rounded-lg px-3 py-2.5 text-sm font-medium {{ $link['active'] ? 'bg-orange-50 text-orange-700' : 'text-slate-700 hover:bg-slate-100' }}"
                        >
                            {{ $link['label'] }}
                        </a>
                    </li>
                @endforeach
            </ul>
        </div>
    </header>

    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="mt-16 border-t border-slate-200 bg-white">
        <div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-4">
                <div class="sm:col-span-2">
                    <div class="flex items-center gap-2.5">
                        <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-orange-400 via-orange-500 to-rose-500 text-sm font-bold text-white shadow-md shadow-orange-500/30">SA</span>
                        <span class="text-base font-semibold text-slate-800">{{ $siteName }}</span>
                    </div>
                    <p class="mt-3 max-w-md text-sm text-slate-500">
                        Rekomendasi produk pilihan dan viral di Shopee dengan harga menarik. Temukan produk favoritmu dan belanja lebih hemat.
                    </p>
                </div>

                <div>
                    <p class="text-sm font-semibold text-slate-800">Jelajahi</p>
                    <ul class="mt-3 space-y-2 text-sm text-slate-500">
                        <li><a href="{{ $home }}" class="hover:text-orange-600">Home</a></li>
                        <li><a href="{{ $home }}#trending" class="hover:text-orange-600">Trending</a></li>
                        <li><a href="{{ $home }}#viral" class="hover:text-orange-600">Viral</a></li>
                        <li><a href="{{ route('products.index') }}" class="hover:text-orange-600">Semua Produk</a></li>
                    </ul>
                </div>

                <div>
                    <p class="text-sm font-semibold text-slate-800">Informasi</p>
                    <ul class="mt-3 space-y-2 text-sm text-slate-500">
                        <li><a href="{{ $home }}#kategori" class="hover:text-orange-600">Kategori</a></li>
                        <li><a href="{{ $home }}#produk-recommended" class="hover:text-orange-600">Rekomendasi</a></li>
                        <li><a href="{{ route('admin.login') }}" class="hover:text-orange-600">Login Admin</a></li>
                    </ul>
                </div>
            </div>

            <div class="mt-8 border-t border-slate-100 pt-6">
                <p class="text-xs text-slate-400">
                    &copy; {{ date('Y') }} {{ $siteName }}. Sebagian tautan merupakan tautan affiliate. Kami dapat menerima komisi tanpa biaya tambahan untuk Anda.
                </p>
            </div>
        </div>
    </footer>

    <script>
        (function () {
            var toggle = document.getElementById('public-nav-toggle');
            var menu = document.getElementById('public-nav-menu');
            toggle && toggle.addEventListener('click', function () {
                var isHidden = menu.classList.toggle('hidden');
                toggle.setAttribute('aria-expanded', isHidden ? 'false' : 'true');
            });
        })();
    </script>
</body>
</html>
