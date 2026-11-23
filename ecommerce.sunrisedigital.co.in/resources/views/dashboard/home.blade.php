@extends('dashboard.layout')

@section('title', 'Dashboard')

@section('content')
    @if (session('onboarding_complete'))
        <div data-onboarding-complete class="fixed inset-0 z-50 flex items-center justify-center bg-[#f7f4ef] px-6 transition-opacity duration-500">
            <div class="flex max-w-md flex-col items-center text-center">
                <div class="onboard-badge grid h-24 w-24 place-items-center rounded-full bg-white shadow-[0_12px_40px_rgba(28,28,28,0.08)]">
                    <svg class="h-14 w-14" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <circle class="onboard-ring" cx="12" cy="12" r="9" />
                        <path class="onboard-tick" d="M8 12.5 10.8 15 16 9.5" />
                    </svg>
                </div>
                <h1 class="onboard-rise mt-6 text-3xl font-semibold tracking-tight text-[#1a1a1a]">Your onboarding process is complete</h1>
                <p class="onboard-rise onboard-rise-delay mt-3 text-sm leading-6 text-[#6f6a64]">You can now access your dashboard.</p>
                <button type="button" data-onboarding-enter class="onboard-rise onboard-rise-late mt-6 inline-flex h-11 items-center rounded-full bg-[#111111] px-5 text-sm font-semibold text-white">Enter dashboard</button>
            </div>
        </div>
    @endif

    <p class="text-xs font-semibold uppercase tracking-[0.16em] text-[#b8860b]">Dashboard</p>
    <h1 class="mt-1 text-3xl font-semibold tracking-tight text-[#1a1a1a]">Overview</h1>
    <p class="mt-2 text-sm text-[#6f6a64]">Signed in as {{ auth()->user()->name }}</p>

    <div class="mt-8 grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        <a href="#services" class="rounded-2xl border border-[#ebe6df] bg-white p-6 shadow-sm hover:border-[#f5b400]">
            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-[#b8860b]">Work</p>
            <p class="mt-2 text-lg font-semibold text-[#1a1a1a]">Services</p>
            <p class="mt-2 text-2xl font-semibold tracking-tight text-[#1a1a1a]">{{ $summary['services'] }}</p>
            <p class="mt-1 text-sm text-[#6f6a64]">Services on this account.</p>
        </a>
        <a href="{{ route('profile.orders') }}" class="rounded-2xl border border-[#ebe6df] bg-white p-6 shadow-sm hover:border-[#f5b400]">
            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-[#b8860b]">Work</p>
            <p class="mt-2 text-lg font-semibold text-[#1a1a1a]">Orders</p>
            <p class="mt-2 text-2xl font-semibold tracking-tight text-[#1a1a1a]">{{ $summary['orders'] }}</p>
            <p class="mt-1 text-sm text-[#6f6a64]">Paid orders you can open.</p>
        </a>
        <a href="#services" class="rounded-2xl border border-[#ebe6df] bg-white p-6 shadow-sm hover:border-[#f5b400]">
            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-[#b8860b]">Setup</p>
            <p class="mt-2 text-lg font-semibold text-[#1a1a1a]">Onboarding</p>
            <p class="mt-2 text-2xl font-semibold tracking-tight text-[#1a1a1a]">{{ $summary['onboarding'] }}</p>
            <p class="mt-1 text-sm text-[#6f6a64]">Questionnaires finished.</p>
        </a>
        <a href="{{ route('profile.orders') }}" class="rounded-2xl border border-[#ebe6df] bg-white p-6 shadow-sm hover:border-[#f5b400]">
            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-[#b8860b]">Billing</p>
            <p class="mt-2 text-lg font-semibold text-[#1a1a1a]">Spent</p>
            <p class="mt-2 text-2xl font-semibold tracking-tight text-[#1a1a1a]">{{ \App\Models\Order::formatMoney($summary['spent']) }}</p>
            <p class="mt-1 text-sm text-[#6f6a64]">Across your paid orders.</p>
        </a>
        <a href="{{ route('dashboard.reports.index') }}" class="rounded-2xl border border-[#ebe6df] bg-white p-6 shadow-sm hover:border-[#f5b400]">
            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-[#b8860b]">Reports</p>
            <p class="mt-2 text-lg font-semibold text-[#1a1a1a]">Reports</p>
            <p class="mt-2 text-2xl font-semibold tracking-tight text-[#1a1a1a]">{{ $summary['reports'] }}</p>
            <p class="mt-1 text-sm text-[#6f6a64]">Weekly, social media, and SEO reports.</p>
        </a>
    </div>

    <div id="services" class="mt-10">
        <h2 class="text-lg font-semibold text-[#1a1a1a]">Your services</h2>
        @if ($orders->isEmpty())
            <p class="mt-4 rounded-2xl border border-[#ebe6df] bg-white px-5 py-8 text-sm text-[#6f6a64]">Your dashboard opens after you buy a service.</p>
        @else
            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                @foreach ($orders as $order)
                    @foreach ($order->items as $item)
                        <a href="{{ route('orders.show', $order) }}" class="rounded-2xl border border-[#ebe6df] bg-white p-6 shadow-sm hover:border-[#f5b400]">
                            <p class="text-xs font-semibold uppercase tracking-[0.16em] text-[#b8860b]">{{ $order->number }}</p>
                            <p class="mt-2 text-lg font-semibold text-[#1a1a1a]">{{ $item->service_name }}</p>
                            <p class="mt-2 text-sm text-[#6f6a64]">{{ $item->duration_label }}</p>
                            <p class="mt-3 inline-flex rounded-full bg-[#fff4e8] px-3 py-1 text-sm font-medium text-[#1a1a1a]">{{ $order->statusLabel() }}</p>
                            @if ($item->onboardingComplete())
                                <p class="mt-3 text-sm font-medium text-[#1f9d55]">Onboarding complete</p>
                            @endif
                        </a>
                    @endforeach
                @endforeach
            </div>
        @endif
    </div>
@endsection
