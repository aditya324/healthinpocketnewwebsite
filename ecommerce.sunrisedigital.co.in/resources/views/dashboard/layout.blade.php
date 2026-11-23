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
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 px-5 py-6">
                <img src="{{ asset('assets/logo/logo.png') }}" alt="" class="h-10 w-auto rounded-md bg-white px-1.5">
                <span>
                    <span class="block text-sm font-semibold leading-none">Sunrise</span>
                    <span class="mt-1 block text-xs text-white/50">Dashboard</span>
                </span>
            </a>

            <nav class="flex flex-1 flex-col gap-6 px-3">
                <a href="{{ route('dashboard') }}" @class([
                    'flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium',
                    'bg-white/10 text-white' => request()->routeIs('dashboard'),
                    'text-white/70 hover:bg-white/5 hover:text-white' => ! request()->routeIs('dashboard'),
                ])>
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                        <rect width="7" height="9" x="3" y="3" rx="1"/>
                        <rect width="7" height="5" x="14" y="3" rx="1"/>
                        <rect width="7" height="9" x="14" y="12" rx="1"/>
                        <rect width="7" height="5" x="3" y="16" rx="1"/>
                    </svg>
                    Overview
                </a>

                <div>
                    <p class="px-3 text-[11px] font-semibold uppercase tracking-[0.16em] text-white/40">Account</p>
                    <div class="mt-2 flex flex-col gap-1">
                        <a href="{{ route('dashboard.reports.index') }}" @class([
                            'flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium',
                            'bg-[#f5b400] text-[#1a1a1a]' => request()->routeIs('dashboard.reports.*'),
                            'text-white/70 hover:bg-white/5 hover:text-white' => ! request()->routeIs('dashboard.reports.*'),
                        ])>
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"/>
                                <path d="M14 2v6h6"/>
                                <path d="M8 13h8"/>
                                <path d="M8 17h8"/>
                            </svg>
                            Reports
                        </a>
                        <a href="{{ route('profile.orders') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-white/70 hover:bg-white/5 hover:text-white">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4Z"/>
                                <path d="M3 6h18"/>
                                <path d="M16 10a4 4 0 0 1-8 0"/>
                            </svg>
                            Orders
                        </a>
                        <a href="{{ route('profile.edit') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-white/70 hover:bg-white/5 hover:text-white">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
                                <circle cx="12" cy="7" r="4"/>
                            </svg>
                            Profile
                        </a>
                        <a href="{{ route('home') }}" class="flex items-center gap-3 rounded-xl px-3 py-2.5 text-sm font-medium text-white/70 hover:bg-white/5 hover:text-white">
                            <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                                <path d="M9 22V12h6v10"/>
                            </svg>
                            Shop
                        </a>
                    </div>
                </div>
            </nav>

            <div class="border-t border-white/10 px-4 py-4">
                <p class="truncate text-sm font-medium">{{ auth()->user()->name }}</p>
                <p class="truncate text-xs text-white/45">{{ auth()->user()->email }}</p>
                <form method="POST" action="{{ route('logout') }}" class="mt-3">
                    @csrf
                    <button type="submit" class="text-sm text-white/70 hover:text-white">Log out</button>
                </form>
            </div>
        </aside>

        <div class="flex min-w-0 flex-1 flex-col">
            <header class="flex items-center justify-between gap-4 border-b border-[#ebe6df] bg-white px-4 py-3 md:hidden">
                <a href="{{ route('dashboard') }}" class="text-sm font-semibold">Sunrise Dashboard</a>
                <nav class="flex items-center gap-4 text-sm">
                    <a href="{{ route('dashboard') }}" @class(['font-semibold text-[#1a1a1a]' => request()->routeIs('dashboard')])>Overview</a>
                    <a href="{{ route('dashboard.reports.index') }}" @class(['font-semibold text-[#1a1a1a]' => request()->routeIs('dashboard.reports.*')])>Reports</a>
                    <a href="{{ route('profile.orders') }}">Orders</a>
                    <a href="{{ route('profile.edit') }}">Profile</a>
                    <a href="{{ route('home') }}">Shop</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-[#6f6a64]">Log out</button>
                    </form>
                </nav>
            </header>

            <main class="mx-auto w-full max-w-6xl flex-1 px-4 py-8 sm:px-6 lg:px-10">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
