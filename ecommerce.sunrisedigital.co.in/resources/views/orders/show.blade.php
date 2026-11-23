<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @include('partials.favicon')
    <title>{{ $order->number }} — {{ config('app.name', 'Sunrise') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col bg-[#f7f4ef] text-[#1c1c1c] antialiased">
    @include('partials.store-header')

    <main class="mx-auto w-full max-w-xl flex-1 px-6 py-10">
        @if (session('status'))
            <p class="mb-6 rounded-xl border border-[#ead89a] bg-[#fff8e6] px-4 py-3 text-sm font-medium text-[#1a1a1a]">{{ session('status') }}</p>
        @endif

        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-[#b8860b]">Order {{ $order->number }}</p>
        <h1 class="mt-2 text-3xl font-semibold tracking-tight text-[#1a1a1a]">{{ $order->name }}</h1>
        <p class="mt-2 text-sm text-[#6f6a64]">{{ $order->email }}</p>
        <p class="mt-3 inline-flex rounded-full bg-[#fff4e8] px-3 py-1 text-sm font-medium text-[#1a1a1a]">{{ $order->statusLabel() }}</p>
        @if ($order->needsOnboarding())
            <div class="mt-4 rounded-xl border border-[#ead89a] bg-[#fff8e6] px-4 py-3">
                <p class="text-sm font-medium text-[#1a1a1a]">Your onboarding process is still pending.</p>
                <a href="{{ route('orders.onboarding', $order) }}" class="mt-3 inline-flex h-10 items-center rounded-full bg-[#111111] px-4 text-sm font-medium text-white">Finish onboarding</a>
            </div>
        @elseif (in_array($order->status, ['placed', 'in_progress', 'delivered'], true))
            <a href="{{ route('dashboard') }}" class="mt-4 inline-flex h-10 items-center rounded-full bg-[#111111] px-4 text-sm font-medium text-white">Open dashboard</a>
        @endif
        @if ($order->customer_note)
            <p class="mt-4 rounded-2xl border border-[#ead89a] bg-[#fff8e6] px-4 py-3 text-sm leading-6 text-[#1a1a1a]">{{ $order->customer_note }}</p>
        @endif
        <a href="{{ route('orders.invoice', $order) }}" class="mt-4 inline-flex text-sm font-medium text-[#c4a035]">Download invoice</a>
        @if ($order->status === 'refunded')
            <p class="mt-2 text-sm font-medium text-[#9b2c2c]">Refunded ₹{{ number_format((float) $order->refunded_amount, 2, '.', ',') }}</p>
        @elseif ((float) $order->refunded_amount > 0)
            <p class="mt-2 text-sm font-medium text-[#9b2c2c]">Partly refunded ₹{{ number_format((float) $order->refunded_amount, 2, '.', ',') }}</p>
        @endif

        <ul class="mt-8 divide-y divide-[#f0ebe3] rounded-2xl border border-[#f0ebe3] bg-white">
            @foreach ($order->items as $item)
                <li class="flex items-start justify-between gap-4 px-5 py-4">
                    <div>
                        <p class="font-semibold text-[#1a1a1a]">{{ $item->service_name }}</p>
                        <p class="mt-1 text-sm text-[#6f6a64]">{{ $item->duration_label }}</p>
                        @if ($item->onboardingComplete())
                            <dl class="mt-3 flex flex-col gap-2">
                                @foreach ($item->onboardingQuestions() as $index => $question)
                                    <div>
                                        <dt class="text-sm font-medium text-[#1a1a1a]">{{ $question['prompt'] }}</dt>
                                        <dd class="text-sm text-[#6f6a64]">
                                            @if ($question['answer'] ?? null)
                                                {{ $question['answer'] }}
                                            @elseif (empty($question['audio']))
                                                Not answered
                                            @endif
                                            @if (! empty($question['audio']))
                                                <audio controls preload="none" src="{{ route('orders.onboarding.audio', [$order, $item, $index]) }}" class="mt-2 w-full"></audio>
                                            @endif
                                        </dd>
                                    </div>
                                @endforeach
                            </dl>
                        @endif
                    </div>
                    <p class="font-semibold text-[#1a1a1a]">{{ $item->money() }}</p>
                </li>
            @endforeach
        </ul>

        @if ((float) $order->discount > 0)
            <p class="mt-4 text-right text-sm text-[#1f9d55]">Discount @if ($order->coupon_code)({{ $order->coupon_code }}) @endif−₹{{ number_format((float) $order->discount, 2, '.', ',') }}</p>
        @endif
        <p class="mt-2 text-right text-sm text-[#6f6a64]">Estimated GST (18%) ₹{{ number_format((float) $order->gst, 2, '.', ',') }}</p>
        <p class="mt-2 text-right text-xl font-semibold text-[#1a1a1a]">{{ $order->money() }}</p>

        @if ($order->subscriptions->isNotEmpty())
            <div class="mt-8 flex flex-col gap-4">
                @foreach ($order->subscriptions as $subscription)
                    @include('partials.subscription-card', ['subscription' => $subscription])
                @endforeach
            </div>
        @endif
    </main>

    @include('partials.store-footer')
</body>
</html>
