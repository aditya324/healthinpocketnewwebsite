<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @include('partials.favicon')
    <title>@yield('title') — {{ config('app.name', 'Sunrise') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#f6f3ee] text-[#1c1c1c] antialiased">
    <div class="flex min-h-screen">
        <aside class="sticky top-0 hidden h-screen w-60 shrink-0 flex-col bg-[#111111] text-white md:flex">
            <a href="{{ route('reports.home') }}" class="flex items-center gap-3 px-5 py-6">
                <img src="{{ asset('assets/logo/logo.png') }}" alt="" class="h-10 w-auto rounded-md bg-white px-1.5">
                <span>
                    <span class="block text-sm font-semibold leading-none">Sunrise</span>
                    <span class="mt-1 block text-xs text-white/50">Reports</span>
                </span>
            </a>

            <nav class="flex flex-1 flex-col gap-1 px-3">
                <a href="{{ route('reports.home') }}" @class([
                    'flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium',
                    'bg-white/10 text-white' => request()->routeIs('reports.home'),
                    'text-white/70 hover:bg-white/5 hover:text-white' => ! request()->routeIs('reports.home'),
                ])>
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                    Clients
                </a>
            </nav>

            <div class="border-t border-white/10 px-4 py-4">
                <p class="truncate text-sm font-medium">{{ auth('reporter')->user()->name }}</p>
                <p class="truncate text-xs text-white/45">{{ auth('reporter')->user()->email }}</p>
                <form method="POST" action="{{ route('reports.logout') }}" class="mt-3">
                    @csrf
                    <button type="submit" class="text-sm text-white/70 hover:text-white">Log out</button>
                </form>
            </div>
        </aside>

        <div class="flex min-w-0 flex-1 flex-col">
            <header class="flex items-center justify-between gap-4 border-b border-[#ebe6df] bg-white px-4 py-3 md:hidden">
                <a href="{{ route('reports.home') }}" class="text-sm font-semibold">Sunrise Reports</a>
                <nav class="flex items-center gap-4 text-sm">
                    <a href="{{ route('reports.home') }}" class="font-semibold text-[#1a1a1a]">Clients</a>
                    <form method="POST" action="{{ route('reports.logout') }}">
                        @csrf
                        <button type="submit" class="text-[#6f6a64]">Log out</button>
                    </form>
                </nav>
            </header>

            <main class="mx-auto w-full max-w-6xl flex-1 px-4 py-8 sm:px-6 lg:px-10">
                @if (session('status'))
                    <p class="mb-6 rounded-xl border border-[#ead89a] bg-[#fff8e6] px-4 py-3 text-sm font-medium text-[#1a1a1a]">{{ session('status') }}</p>
                @endif
                @yield('content')
            </main>
        </div>
    </div>

    <div data-admin-confirm class="fixed inset-0 z-50 hidden items-center justify-center p-4" role="dialog" aria-modal="true" aria-labelledby="report-confirm-title">
        <button type="button" data-confirm-dismiss class="absolute inset-0 bg-[#111111]/50" aria-label="Close"></button>
        <div class="relative w-full max-w-md rounded-2xl bg-white p-6 shadow-xl">
            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-[#b8860b]">Confirm</p>
            <h2 id="report-confirm-title" data-confirm-title class="mt-2 text-xl font-semibold text-[#1a1a1a]"></h2>
            <p data-confirm-body class="mt-2 text-sm leading-6 text-[#6f6a64]"></p>
            <div class="mt-6 flex items-center justify-end gap-3">
                <button type="button" data-confirm-dismiss class="inline-flex h-11 items-center rounded-full border border-[#e4e0da] px-5 text-sm font-medium text-[#1a1a1a]">Cancel</button>
                <button type="button" data-confirm-accept class="inline-flex h-11 items-center rounded-full px-5 text-sm font-semibold"></button>
            </div>
        </div>
    </div>
    @stack('scripts')
</body>
</html>
