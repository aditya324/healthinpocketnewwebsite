@if ($deals->isNotEmpty())
    <section class="bg-[#FFEADD]" aria-labelledby="todays-deals">
        <div class="h-1 bg-[#f5b400]" aria-hidden="true"></div>
        <div class="mx-auto w-full max-w-[90.5vw] px-10 py-14 sm:px-20 sm:py-16 lg:px-28">
            <div class="flex flex-wrap items-end justify-between gap-4">
                <div>
                    <p class="inline-flex items-center gap-2 rounded-full bg-[#f5b400] px-3 py-1 text-[11px] font-semibold tracking-[0.14em] text-[#1a1a1a] uppercase">
                        <span aria-hidden="true">✦</span>
                        On sale
                    </p>
                    <h2 id="todays-deals" class="mt-4 text-2xl font-semibold tracking-tight text-[#1a1a1a] sm:text-3xl">Today's Deals</h2>
                    <p class="mt-2 max-w-xl text-sm leading-relaxed text-[#5c574f] sm:text-base">Selected services priced below their usual rate.</p>
                </div>
                <a href="{{ route('deals') }}" class="inline-flex shrink-0 items-center gap-2 rounded-lg bg-[#1a1a1a] px-5 py-2.5 text-sm font-medium text-white">
                    See all deals
                    <span aria-hidden="true">→</span>
                </a>
            </div>

            <div class="mt-8 grid gap-5 md:grid-cols-3">
                @foreach ($deals as $deal)
                    @include('partials.store-deal-card', ['deal' => $deal])
                @endforeach
            </div>
        </div>
    </section>
@endif
