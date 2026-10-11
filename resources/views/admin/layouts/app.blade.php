<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') &middot; {{ $siteName }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="sa-admin-page min-h-screen text-slate-800 antialiased">
    @php
        $navItems = [
            ['label' => 'Dashboard', 'route' => 'admin.dashboard', 'icon' => 'M3 12l9-9 9 9M5 10v10a1 1 0 001 1h3a1 1 0 001-1v-4h4v4a1 1 0 001 1h3a1 1 0 001-1V10'],
            ['label' => 'Produk', 'route' => 'admin.products.index', 'match' => 'admin.products.*', 'icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4'],
            ['label' => 'Kategori', 'route' => 'admin.categories.index', 'match' => 'admin.categories.*', 'icon' => 'M4 6h16M4 12h16M4 18h7'],
            ['label' => 'Review', 'route' => 'admin.reviews.index', 'match' => 'admin.reviews.*', 'icon' => 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.196-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118L2.05 10.1c-.783-.57-.38-1.81.588-1.81h4.915a1 1 0 00.95-.69l1.519-4.674z'],
            ['label' => 'Statistik', 'route' => 'admin.statistics.index', 'match' => 'admin.statistics.*', 'icon' => 'M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z'],
            ['label' => 'Pengaturan', 'route' => 'admin.settings.index', 'match' => 'admin.settings.*', 'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065zM15 12a3 3 0 11-6 0 3 3 0 016 0z'],
        ];
    @endphp

    <div class="min-h-screen lg:pl-64">
        <!-- Sidebar -->
        <div id="sidebar-overlay" class="fixed inset-0 z-30 hidden bg-slate-900/50 lg:hidden"></div>

        <aside
            id="admin-sidebar"
            class="fixed inset-y-0 left-0 z-40 flex w-64 -translate-x-full flex-col border-r border-slate-200 bg-white transition-transform duration-200 ease-in-out lg:translate-x-0"
            aria-label="Sidebar"
        >
            <div class="flex h-16 items-center gap-3 border-b border-slate-200 px-5">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-orange-400 via-orange-500 to-rose-500 text-sm font-bold text-white shadow-md shadow-orange-500/30">SA</span>
                <div class="leading-tight">
                    <p class="text-sm font-semibold text-slate-800">{{ $siteName }}</p>
                    <p class="text-xs text-slate-500">Panel Admin</p>
                </div>
            </div>

            <nav class="flex-1 space-y-1 overflow-y-auto p-3">
                @foreach ($navItems as $item)
                    @php $isActive = $item['route'] && request()->routeIs($item['match'] ?? $item['route']); @endphp
                    @if ($item['route'])
                        <a
                            href="{{ route($item['route']) }}"
                            class="flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition {{ $isActive ? 'bg-orange-50 text-orange-700' : 'text-slate-600 hover:bg-slate-100 hover:text-slate-900' }}"
                        >
                            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" />
                            </svg>
                            {{ $item['label'] }}
                        </a>
                    @else
                        <span
                            class="flex cursor-not-allowed items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-slate-400"
                            title="Segera hadir"
                        >
                            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" />
                            </svg>
                            {{ $item['label'] }}
                            <span class="ml-auto rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold uppercase text-slate-400">Soon</span>
                        </span>
                    @endif
                @endforeach
            </nav>

            <div class="border-t border-slate-200 p-3">
                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button
                        type="submit"
                        class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-red-600 transition hover:bg-red-50"
                    >
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        <!-- Content -->
        <div class="flex min-h-screen flex-col">
            <!-- Header -->
            <header class="sticky top-0 z-20 flex h-16 items-center gap-4 border-b border-slate-200 bg-white/90 px-4 backdrop-blur sm:px-6">
                <button
                    type="button"
                    id="sidebar-toggle"
                    class="inline-flex h-10 w-10 items-center justify-center rounded-lg text-slate-600 hover:bg-slate-100 lg:hidden"
                    aria-label="Buka menu"
                >
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.7">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>

                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-semibold text-slate-800">{{ $siteName }}</p>
                    <p class="truncate text-xs text-slate-500">@yield('title', 'Dashboard')</p>
                </div>

                <div class="relative" id="profile-dropdown">
                    <button
                        type="button"
                        id="profile-toggle"
                        class="flex items-center gap-2 rounded-full border border-slate-200 py-1 pl-1 pr-3 transition hover:bg-slate-50"
                    >
                        <span class="flex h-8 w-8 items-center justify-center rounded-full bg-orange-100 text-sm font-semibold text-orange-700">
                            {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                        </span>
                        <span class="hidden text-sm font-medium text-slate-700 sm:block">{{ auth()->user()->name }}</span>
                        <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div
                        id="profile-menu"
                        class="absolute right-0 mt-2 hidden w-56 rounded-xl border border-slate-200 bg-white py-1.5 shadow-lg"
                    >
                        <div class="border-b border-slate-100 px-4 py-2.5">
                            <p class="truncate text-sm font-semibold text-slate-800">{{ auth()->user()->name }}</p>
                            <p class="truncate text-xs text-slate-500">{{ auth()->user()->email }}</p>
                            <span class="mt-1 inline-block rounded-full bg-orange-50 px-2 py-0.5 text-[10px] font-semibold uppercase text-orange-600">{{ auth()->user()->role }}</span>
                        </div>
                        <a href="{{ route('admin.settings.index') }}" class="block px-4 py-2 text-sm text-slate-600 hover:bg-slate-50">Pengaturan</a>
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <button type="submit" class="block w-full px-4 py-2 text-left text-sm text-red-600 hover:bg-red-50">Logout</button>
                        </form>
                    </div>
                </div>
            </header>

            <main class="flex-1 px-4 py-6 sm:px-6 lg:px-8">
                @yield('content')
            </main>
        </div>
    </div>

    <script>
        (function () {
            var sidebar = document.getElementById('admin-sidebar');
            var overlay = document.getElementById('sidebar-overlay');
            var toggle = document.getElementById('sidebar-toggle');

            function openSidebar() {
                sidebar.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
            }
            function closeSidebar() {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            }
            toggle && toggle.addEventListener('click', openSidebar);
            overlay && overlay.addEventListener('click', closeSidebar);

            var profileToggle = document.getElementById('profile-toggle');
            var profileMenu = document.getElementById('profile-menu');
            profileToggle && profileToggle.addEventListener('click', function (e) {
                e.stopPropagation();
                profileMenu.classList.toggle('hidden');
            });
            document.addEventListener('click', function () {
                profileMenu && profileMenu.classList.add('hidden');
            });
        })();
    </script>
</body>
</html>
