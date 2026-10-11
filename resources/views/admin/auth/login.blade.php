<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Login Admin &middot; {{ $siteName }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-orange-600 text-slate-800 antialiased">
    <div class="sa-hero flex min-h-screen items-center justify-center px-4 py-10">
        <div class="pointer-events-none absolute inset-0" aria-hidden="true">
            <x-sphere color="peach" size="h-64 w-64" class="left-[-4rem] top-[-3rem] opacity-60 float-slow" />
            <x-sphere color="rose" size="h-24 w-24" class="right-[8%] top-[14%] opacity-60 float-slower" />
            <x-ring size="h-32 w-32" class="right-[4%] bottom-[12%] opacity-40 spin-slow" />
            <x-shape size="h-16 w-16" class="left-[10%] bottom-[16%] opacity-50 float-reverse" />
        </div>
        <div class="relative z-10 w-full max-w-md">
            <div class="mb-6 text-center">
                <div class="mx-auto mb-3 flex h-14 w-14 items-center justify-center rounded-2xl bg-white/15 text-2xl font-bold text-white ring-1 ring-white/30 backdrop-blur">
                    SA
                </div>
                <h1 class="text-2xl font-semibold text-white">Shopee Affiliate</h1>
                <p class="mt-1 text-sm text-white/80">Masuk ke panel administrator</p>
            </div>

            <div class="rounded-2xl bg-white p-6 text-slate-800 shadow-xl ring-1 ring-black/5 sm:p-8">
                @if (session('status'))
                    <div class="mb-4 rounded-lg bg-emerald-50 px-4 py-3 text-sm text-emerald-700 ring-1 ring-emerald-200">
                        {{ session('status') }}
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mb-4 rounded-lg bg-red-50 px-4 py-3 text-sm text-red-700 ring-1 ring-red-200" role="alert">
                        <ul class="list-inside list-disc space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.login') }}" class="space-y-5" novalidate>
                    @csrf

                    <div>
                        <label for="email" class="mb-1.5 block text-sm font-medium text-slate-700">Email</label>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            value="{{ old('email') }}"
                            required
                            autofocus
                            autocomplete="username"
                            placeholder="admin@affiliate.test"
                            class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 text-sm shadow-sm outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-200 @error('email') border-red-400 focus:border-red-500 focus:ring-red-200 @enderror"
                        >
                    </div>

                    <div>
                        <label for="password" class="mb-1.5 block text-sm font-medium text-slate-700">Password</label>
                        <div class="relative">
                            <input
                                id="password"
                                name="password"
                                type="password"
                                required
                                autocomplete="current-password"
                                placeholder="••••••••"
                                class="w-full rounded-lg border border-slate-300 px-3.5 py-2.5 pr-12 text-sm shadow-sm outline-none transition focus:border-orange-500 focus:ring-2 focus:ring-orange-200 @error('password') border-red-400 focus:border-red-500 focus:ring-red-200 @enderror"
                            >
                            <button
                                type="button"
                                data-toggle-password
                                aria-label="Tampilkan password"
                                class="absolute inset-y-0 right-0 flex items-center px-3 text-xs font-medium text-slate-500 hover:text-orange-600"
                            >
                                Lihat
                            </button>
                        </div>
                    </div>

                    <label class="flex items-center gap-2 text-sm text-slate-600">
                        <input
                            type="checkbox"
                            name="remember"
                            value="1"
                            @checked(old('remember'))
                            class="h-4 w-4 rounded border-slate-300 text-orange-600 focus:ring-orange-500"
                        >
                        Ingat saya
                    </label>

                    <button
                        type="submit"
                        class="flex w-full items-center justify-center rounded-lg bg-orange-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-300 focus:ring-offset-2"
                    >
                        Login
                    </button>
                </form>
            </div>

            <p class="mt-6 text-center text-xs text-white/70">
                &copy; {{ date('Y') }} Shopee Affiliate. Hanya untuk administrator.
            </p>
        </div>
    </div>

    <script>
        document.querySelectorAll('[data-toggle-password]').forEach(function (button) {
            button.addEventListener('click', function () {
                var input = button.closest('.relative').querySelector('input');
                var isHidden = input.type === 'password';
                input.type = isHidden ? 'text' : 'password';
                button.textContent = isHidden ? 'Sembunyi' : 'Lihat';
            });
        });
    </script>
</body>
</html>
