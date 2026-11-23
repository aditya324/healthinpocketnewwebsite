<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @include('partials.favicon')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Onboarding — {{ $order->number }} — {{ config('app.name', 'Sunrise') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="flex min-h-screen flex-col bg-[#f7f4ef] text-[#1c1c1c] antialiased">
    @include('partials.store-header')

    <main class="mx-auto w-full max-w-2xl flex-1 px-6 py-10">
        @if (session('status'))
            <p class="mb-6 rounded-xl border border-[#ead89a] bg-[#fff8e6] px-4 py-3 text-sm font-medium text-[#1a1a1a]">{{ session('status') }}</p>
        @endif

        <p class="text-xs font-semibold uppercase tracking-[0.16em] text-[#b8860b]">Order {{ $order->number }}</p>
        <h1 class="mt-2 text-3xl font-semibold tracking-tight text-[#1a1a1a]">Tell us about the work</h1>
        <p class="mt-2 text-sm text-[#6f6a64]">Type an answer, or tap the microphone and speak. Your words appear in that box. If you leave, the answers stay so you can continue from the order.</p>

        <form method="POST" action="{{ route('orders.onboarding.update', $order) }}" class="mt-8 flex flex-col gap-8" data-onboarding data-onboarding-draft="{{ route('orders.onboarding.draft', $order) }}">
            @csrf
            @foreach ($order->items as $item)
                @if ($item->needsOnboarding())
                    <section class="rounded-2xl border border-[#f0ebe3] bg-white p-5">
                        <h2 class="text-lg font-semibold text-[#1a1a1a]">{{ $item->service_name }}</h2>
                        <div class="mt-4 flex flex-col gap-5">
                            @foreach ($item->onboardingQuestions() as $index => $question)
                                <div class="block">
                                    <span class="text-sm font-medium text-[#1a1a1a]">
                                        {{ $question['prompt'] }}
                                        @if ($question['required'] ?? false)
                                            <span class="text-[#9b2c2c]">*</span>
                                        @endif
                                    </span>
                                    @if ($question['help'] ?? null)
                                        <span class="mt-1 block text-sm text-[#6f6a64]">{{ $question['help'] }}</span>
                                    @endif
                                    <div class="mt-2" data-voice data-voice-url="{{ route('orders.onboarding.audio.store', [$order, $item, $index]) }}">
                                        <div class="relative">
                                            <textarea name="items[{{ $item->id }}][answers][{{ $index }}]" rows="3" class="w-full rounded-xl border border-[#ece7e0] px-3 py-2 pr-12 text-sm">{{ old('items.'.$item->id.'.answers.'.$index, $question['answer']) }}</textarea>
                                            <button type="button" data-voice-record class="absolute bottom-2 right-2 inline-flex h-8 w-8 items-center justify-center rounded-full text-[#8a8680] hover:bg-[#f4f1eb] hover:text-[#1a1a1a] aria-pressed:bg-[#fff1f0] aria-pressed:text-[#c0392b]" aria-label="{{ empty($question['audio']) ? 'Answer with microphone' : 'Record again' }}" aria-pressed="false">
                                                <span data-voice-dot class="absolute right-1.5 top-1.5 hidden h-1.5 w-1.5 rounded-full bg-[#c0392b]" aria-hidden="true"></span>
                                                <svg data-voice-mic class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                                    <path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"/>
                                                    <path d="M19 10v2a7 7 0 0 1-14 0v-2"/>
                                                    <line x1="12" x2="12" y1="19" y2="22"/>
                                                </svg>
                                            </button>
                                        </div>
                                        <p data-voice-status class="mt-1 hidden text-sm text-[#6f6a64]" aria-live="polite"></p>
                                        <audio data-voice-player controls preload="none" @class(['mt-2 w-full', 'hidden' => empty($question['audio'])]) @if (! empty($question['audio'])) src="{{ route('orders.onboarding.audio', [$order, $item, $index]) }}" @endif></audio>
                                    </div>
                                    @error('items.'.$item->id.'.answers.'.$index)
                                        <span class="mt-1 block text-sm text-[#9b2c2c]">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endforeach
                        </div>
                    </section>
                @endif
            @endforeach

            <button type="submit" class="inline-flex h-11 items-center justify-center self-start rounded-full bg-[#111111] px-5 text-sm font-semibold text-white">Save answers</button>
        </form>
    </main>

    @include('partials.store-footer')
</body>
</html>
