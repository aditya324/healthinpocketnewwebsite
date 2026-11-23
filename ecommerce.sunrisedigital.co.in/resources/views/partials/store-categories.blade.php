<section class="bg-[#f7f4ef]" aria-labelledby="shop-by-category">
    <div class="mx-auto w-full container px-6 py-14 sm:px-8 sm:py-16">
        <div class="flex items-end justify-between gap-6">
            <div>
                <h2 id="shop-by-category" class="text-2xl font-semibold tracking-tight text-[#1a1a1a] sm:text-3xl">Shop by Category</h2>
                <p class="mt-2 max-w-xl text-sm leading-relaxed text-[#5c574f] sm:text-base">Explore specialized digital capabilities for your specific growth needs.</p>
            </div>
            <a href="{{ route('categories.index') }}" class="shrink-0 text-sm font-semibold tracking-wide text-[#a68420] hover:text-[#8a6914]">
                View All
                <span aria-hidden="true">→</span>
            </a>
        </div>

        <div class="mt-10 grid grid-cols-2 gap-x-5 gap-y-8 sm:grid-cols-4 sm:gap-x-8">
            @foreach ($homeCategories as $category)
                <a href="{{ route('categories.show', $category) }}" class="group text-center">
                    <span class="block aspect-[2.4/1] overflow-hidden rounded-full bg-white shadow-sm">
                        @if ($category->image)
                            <img
                                src="{{ asset('assets/categories/'.$category->image) }}"
                                alt=""
                                class="h-full w-full object-cover transition duration-300 group-hover:scale-105"
                            >
                        @elseif ($category->slug === 'social-media')
                            <svg class="h-full w-full transition duration-300 group-hover:scale-105" viewBox="0 0 288 120" data-category-icons="social-media" aria-hidden="true">
                                <defs>
                                    <linearGradient id="social-tile-gold" x1="0" y1="0" x2="1" y2="1">
                                        <stop offset="0" stop-color="#ffe9ad"/>
                                        <stop offset="1" stop-color="#f5b400"/>
                                    </linearGradient>
                                </defs>
                                <rect width="288" height="120" fill="#fffdf8"/>
                                <ellipse cx="150" cy="64" rx="96" ry="36" fill="#fff1cc"/>
                                <g transform="rotate(9 214 62)">
                                    <rect x="178" y="28" width="68" height="66" rx="12" fill="#ffe7a3"/>
                                    <rect x="186" y="36" width="52" height="28" rx="7" fill="#fff6df"/>
                                    <rect x="186" y="70" width="34" height="4" rx="2" fill="#fff"/>
                                    <rect x="186" y="78" width="22" height="4" rx="2" fill="#fff6df"/>
                                </g>
                                <rect x="86" y="18" width="104" height="84" rx="14" fill="#fff" stroke="#f3e4c2" stroke-width="1.5"/>
                                <rect x="96" y="28" width="84" height="42" rx="9" fill="url(#social-tile-gold)"/>
                                <circle cx="168" cy="58" r="13" fill="#1a1a1a"/>
                                <path d="M164 51.5v13l11-6.5-11-6.5Z" fill="#f5b400"/>
                                <circle cx="112" cy="84" r="7" fill="#1a1a1a"/>
                                <rect x="126" y="80" width="42" height="4" rx="2" fill="#1a1a1a"/>
                                <rect x="126" y="88" width="26" height="3" rx="1.5" fill="#ead9b0"/>
                                <path d="M198 34c0-3.2 2.4-5.2 4.6-5.2 1.4 0 2.4.7 3 1.6.6-.9 1.6-1.6 3-1.6 2.2 0 4.6 2 4.6 5.2 0 3.6-4.2 6.6-7.6 8.4-3.4-1.8-7.6-4.8-7.6-8.4Z" fill="#f5b400"/>
                            </svg>
                        @endif
                    </span>
                    <span class="mt-4 block text-[15px] font-semibold leading-snug tracking-tight text-[#1a1a1a] sm:text-base">{{ $category->name }}</span>
                </a>
            @endforeach
        </div>
    </div>
</section>
